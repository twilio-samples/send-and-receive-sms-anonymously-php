# Send and Receive SMS Anonymously with PHP and Twilio

This privacy-conscious app uses a Twilio phone number to relay SMS messages to and from your phone, masking your phone number from the public. For more details, see [Twilio's blog post about SMS forwarding][twilio_sms_forwarding_url].

## Prerequisites/Requirements

To run the code, you will need the following:

- PHP 8.3
- [Composer][composer_url] installed globally
- A network testing tool such as [curl][curl_url] or [Postman][postman_url]
- [ngrok][ngrok_url] and a free ngrok account
- A Twilio account (free or paid) with an active phone number that can send SMS.
  If you are new to Twilio, [create a free account][try_twilio_url].

[composer_url]: https://getcomposer.org
[ngrok_url]: https://ngrok.com/
[try_twilio_url]: https://www.twilio.com/try-twilio
[curl_url]: https://curl.se/
[postman_url]: https://www.postman.com/
[twilio_sms_forwarding_url]: https://www.twilio.com/blog/sms-forwarding-and-responding-using-twilio-and-javascript