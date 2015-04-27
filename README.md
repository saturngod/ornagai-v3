# Orangai V3

Update the `config.php` with yoru db information.

You can import mysql database from `sample_ornagai.sql.zip`

You will need rsa private key and Modulus.

## Generate RSA Key

```
$ openssl genrsa -out key.pem
$ cat key.pem | pbcopy
```

Paste the code in to the config.php

```php
$config['privatekey'] = "PASTE THE CODE HERE";
```

```
$ openssl rsa -in key.pem -noout -modulus
```

Copy the Modulus. Example `9CAF2D1CBB4F823C46457DA5EAE24F3422BE0E2B3E81E3CF04F5C00B16487DDF96BD901F39577F7F3650882A9292BBB0272D872A28E867FA0A89A06DEE4B73A`

Search , `rsa.setPublic` and paste the code in `js/app.js`.

Example

```js
rsa.setPublic("9CAF2D1CBB4F823C46457DA5EAE24F3422BE0E2B3E81E3CF04F5C00B16487DDF96BD901F39577F7F3650882A9292BBB0272D872A28E867FA0A89A06DEE4B73A","10001");
```

## Requirement

You need to install composer first.

```
php composer.phar install
```

## Libraries

### Front-End

- [Angular.JS](https://angularjs.org)
- [rsa.js](http://www-cs-students.stanford.edu/%7Etjw/jsbn/)
- [jQuery](https://jquery.com)
- [typeahead.js](https://twitter.github.io/typeahead.js/)
- [Sweetalert](http://t4t5.github.io/sweetalert/)
- [Marked](https://github.com/chjj/marked)
- [speech-synthesis](https://travis-ci.org/janantala/speech-synthesis)

### Back-End

- [Slim Framework](http://www.slimframework.com)
- [MySQL](https://www.mysql.com)
- [Composer](https://getcomposer.org)
- [phpseclib](https://github.com/phpseclib/phpseclib)