# Send and Receive SMS Anonymously with PHP and Twilio

This privacy-conscious app uses a Twilio phone number to relay SMS messages to and from your phone, masking your phone number from the public. For more details, see [Twilio's blog post about SMS forwarding][twilio_sms_forwarding_url].

## Prerequisites/Requirements

To run the code, you will need the following:

- PHP 8.3
- [Composer][composer_url] installed globally
- [ngrok][ngrok_url] and a free ngrok account
- A Twilio account (free or paid) with an active phone number that can send SMS.
  If you are new to Twilio, [create a free account][try_twilio_url].

## Getting Started

After cloning the code to wherever you store your Go projects, and change into the project directory.
Then, copy _.env.example_ as _.env_, by running the following command:

```bash
cp -v .env.example .env
```

Then, set `MY_PHONE_NUMBER` to the phone number (in [E.164 format][twilio_e164_format_url]) that you want to receive SMS.

When that's done, run the following command to launch the application:

```php
go run main.go
```

Then, use ngrok to create a secure tunnel between port 8080 on your local development machine and the public internet, making the application publicly accessible, by running the following command.

```php
ngrok http 8080
```

[composer_url]: https://getcomposer.org
[ngrok_url]: https://ngrok.com/
[try_twilio_url]: https://www.twilio.com/try-twilio
[twilio_sms_forwarding_url]: https://www.twilio.com/blog/sms-forwarding-and-responding-using-twilio-and-javascript
[twilio_e164_format_url]: https://www.twilio.com/docs/glossary/what-e164 