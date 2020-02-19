<?php
class PaypalIPN
{
    /** @var bool Indicates if the sandbox endpoint is used. */
    private $use_sandbox = false;
    /** @var bool Indicates if the local certificates are used. */
    private $use_local_certs = true;
    /** Production Postback URL */
    const VERIFY_URI = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    /** Response from PayPal indicating validation was successful */
    const VALID = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    const INVALID = 'INVALID';
    /**
     * Sets the IPN verification to sandbox mode (for use when testing,
     * should not be enabled in production).
     * @return void
     */
	 
    public function useSandbox()
    {
        $this->use_sandbox = false;
    }
    /**
     * Sets curl to use php curl's built in certs (may be required in some
     * environments).
     * @return void
     */
    public function usePHPCerts()
    {
        $this->use_local_certs = false;
    }
    /**
     * Determine endpoint to post the verification data to.
     *
     * @return string
     */
    public function getPaypalUri()
    {
        if ($this->use_sandbox) 
		{
			$Subject = "PaypalIPN claims transaction is using sandbox";
			$Text = "This means the transaction is set to use a fake account \r\n";
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
            return self::SANDBOX_VERIFY_URI;
        } else 
		{
			$Subject = "PaypalIPN claims transaction is using live mode";
			$Text = "This means the transaction is set to use actual money account \r\n";
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
            return self::VERIFY_URI;
        }
    }
    /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */
    public function verifyIPN()
    {
        if ( ! count($_POST)) 
		{
			sendGenericEmailFromNoReply("Kiljax@gmail.com", "Post somehow 0 when sent to IPN", "Troubleshooting is fun");
            throw new Exception("Missing POST Data");
        }
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
                if ($keyval[0] === 'payment_date') 
				{
                    if (substr_count($keyval[1], '+') === 1) 
					{
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }
					if (substr_count($keyval[1], ':') === 1) 
					{
                        $keyval[1] = str_replace(':', '%3A', $keyval[1]);
                    }
                }
				if ($keyval[0] === 'payer_email')
				{
                    if (substr_count($keyval[1], '@') === 1) {
                        $keyval[1] = str_replace('@', '%40', $keyval[1]);
                    }
                }
				if ($keyval[0] === 'receiver_email')
				{
                    if (substr_count($keyval[1], '@') === 1) {
                        $keyval[1] = str_replace('@', '%40', $keyval[1]);
                    }
                }
				if ($keyval[0] === 'business')
				{
                    if (substr_count($keyval[1], '@') === 1) {
                        $keyval[1] = str_replace('@', '%40', $keyval[1]);
                    }
                }
				  
                $myPost[$keyval[0]] = $keyval[1];
            }
        }
		
		/*
		$Subject = "PaypalIPN has built this array for verification";
		$Text .= "\r\n The original post data: \r\n";
		$Text .= print_r($raw_post_data, true);
		$Text .= "\r\nThe original array: \r\n";
		$Text .= print_r($raw_post_array, true);
		$Text .= "\r\n The built array in quesiton:  \r\n";
		$Text .= print_r($myPost, true);
		sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
		*/
		
        // Build the body of the verification post request, adding the _notify-validate command.
        $req = 'cmd=_notify-validate';
        $get_magic_quotes_exists = false;
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = (stripslashes($value));
            } else {
                $value = $value;
            }
            $req .= "&$key=$value";
        }
		/*
		$Subject = "PaypalIPN is building this informaiton to send to paypal to verify the information: \r\n";
		$Text = "<br> The comparative: <br>";
		$Text .= "<br> ".print_r($myPost, true)."<br>";
		$Text .= "The information in quesiton:  \r\n";
		$Text .= print_r($req, true);
		sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
		*/
        // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
        $ch = curl_init($this->getPaypalUri());
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // This is often required if the server is missing a global cert bundle, or is using an outdated one.
        if ($this->use_local_certs) {
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");
        }
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: PHP-IPN-Verification-Script',
            'Connection: Close',
        ));
        $res = curl_exec($ch);
        if ( ! ($res)) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
			$Subject = "PaypalIPN stopped due to a cURL error";
			$Text = "The error in quesiton:  \r\n";
			$Text .= "Error: " [$errno]. " ". $errstr;
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
        }
        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        if ($http_code != 200) 
		{
			$Subject = "PaypalIPN received an http code that was not 200";
			$Text = "The http code in quesiton:  \r\n";
			$Text .= "".$http_code."";
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
            throw new Exception("PayPal responded with http code $http_code");
        }
        curl_close($ch);
        // Check if PayPal verifies the IPN data, and if so, return true.
        if ($res == self::VALID)
		{
			$Subject = "PaypalIPN claims verified message";
			$Text = "This means the transaction is supposed to work\r\n";
			$Text .= "HTTP Code received: $http_code \r\n";
			$Text .= print_r($ch,true);
			$Text .= "\r\n The string sent to paypal: \r\n";
			$Text .= print_r($req);
			$Text .="\r\n The orginal my post details: \r\n";
			$Text .= print_r($myPost);
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
            return true;
        } 
		else 
		{
			$Subject = "PaypalIPN claims message unverified";
			$Text = "This means the transaction fails in PayPal's own IPN code \r\n";
			$Text .= "HTTP Code received: $http_code";
			$Text .= print_r($ch,true);
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
            return false;
        }
    }
}