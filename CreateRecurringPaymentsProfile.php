ndbox';	// or 'beta-sandbox' or 'live'

/**
 * Send HTTP POST Request
  *
   * @param	string	The API method name
    * @param	string	The POST Message fields in &name=value pair format
     * @return	array	Parsed HTTP Response body
      */
      function PPHttpPost($methodName_, $nvpStr_) {
      	global $environment;

		$API_UserName = urlencode('my_api_username');
			$API_Password = urlencode('my_api_password');
				$API_Signature = urlencode('my_api_signature');
					$API_Endpoint = "https://api-3t.paypal.com/nvp";
						if("sandbox" === $environment || "beta-sandbox" === $environment) {
								$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
									}
										$version = urlencode('51.0');

											// setting the curl parameters.
												$ch = curl_init();
													curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
														curl_setopt($ch, CURLOPT_VERBOSE, 1);

															// turning off the server and peer verification(TrustManager Concept).
																curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
																	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

																		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
																			curl_setopt($ch, CURLOPT_POST, 1);

																				// NVPRequest for submitting to server
																					$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

																						// setting the nvpreq as POST FIELD to curl
																							curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

																								// getting response from server
																									$httpResponse = curl_exec($ch);

																										if(!$httpResponse) {
																												exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
																													}

																														// Extract the RefundTransaction response details
																															$httpResponseAr = explode("&", $httpResponse);

																																$httpParsedResponseAr = array();
																																	foreach ($httpResponseAr as $i => $value) {
																																			$tmpAr = explode("=", $value);
																																					if(sizeof($tmpAr) > 1) {
																																								$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
																																										}
																																											}

																																												if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
																																														exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
																																															}

																																																return $httpParsedResponseAr;
																																																}

																																																$token = urlencode("token_from_setExpressCheckout");
																																																$paymentAmount = urlencode("payment_amount");
																																																$currencyID = urlencode("USD");						// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
																																																/* code for start of the month <Starts> */

																																																		$date_now = date('d');
																																																				$month_now = date ('m');
																																																						$days = date("t");
																																																								$daysDiff = $days - $date_now;
																																																										if($daysDiff!=0)
																																																												  { 
																																																												  		    if($month_now == 12)
																																																														    			  {
																																																																	  			   $startDateP = mktime(0,0,0,01,01,date('Y')+1) ;
																																																																				   			  }
																																																																							  			else {
																																																																										                 $startDateP = mktime(0,0,0,date('m')+1,01,date('Y')) ;
																																																																												 				 }			
																																																																																 		  }
																																																																																		  		  else {
																																																																																				  				$startDateP = mktime(0,0,0,date('m'),date('d'),date('Y')) ; 
																																																																																											   }
																																																																																											   			   
																																																																																														   $startDate =  urlencode(date("Y-d-mTG:i:sz",$startDateP));
																																																																																														   //$startDate = urlencode("2009-9-6T0:0:0");

																																																																																														   /* code for start of the month <Ends> */
																																																																																														   $billingPeriod = urlencode("Month");				// or "Day", "Week", "SemiMonth", "Year"
																																																																																														   $billingFreq = urlencode("1");						// combination of this and billingPeriod must be at most a year


																																																																																														   $nvpStr="&TOKEN=$token&AMT=$paymentAmount&CURRENCYCODE=$currencyID&PROFILESTARTDATE=$startDate";
																																																																																														   /* code for trail <Starts> */
																																																																																														   $nvpStr .= "&TRIALBILLINGPERIOD=$billingPeriod&TRIALBILLINGFREQUENCY=1&TRIALAMT=$paymentAmount&TRIALTOTALBILLINGCYCLES=1";
																																																																																														   /* code for trail <Ends> */
																																																																																														   $nvpStr .= "&BILLINGPERIOD=$billingPeriod&BILLINGFREQUENCY=$billingFreq";

																																																																																														   $httpParsedResponseAr = PPHttpPost('CreateRecurringPaymentsProfile', $nvpStr);

																																																																																														   if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
																																																																																														   	exit('CreateRecurringPaymentsProfile Completed Successfully: '.print_r($httpParsedResponseAr, true));
																																																																																															} else  {
																																																																																																exit('CreateRecurringPaymentsProfile failed: ' . print_r($httpParsedResponseAr, true));
																																																																																																}

																																																																																																?>
