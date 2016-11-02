# MobWeb_MassResetCustomerPasswords extension for Magento

A simple extension that adds a new link "Send new passwords" to the customer mass actions in the admin panel. The selected customers will be sent a new password using Magento's default "New Password" email.

This is useful if you have imported customer accounts from an external source and need to assign them all a custom password and notify them about it.

## Shell scripts

There are two shell scripts included:

- mass-reset-customer-passwords.php allows to batch process customer accounts and assign and send the passwords for a limited amount of accounts. This makes it a bit easier to process a big amount of accounts.

- reset-and-export-customer-passwords.php allows to reset the passwords of all customer accounts and export the account data (gender, first name, last name, email, new password) as a CSV file. This can then be used to send this information to the customers using an external email service (e.g. Mailchimp).

## Installation

Install using [colinmollenhour/modman](https://github.com/colinmollenhour/modman/).

## Questions? Need help?

Most of my repositories posted here are projects created for customization requests for clients, so they probably aren't very well documented and the code isn't always 100% flexible. If you have a question or are confused about how something is supposed to work, feel free to get in touch and I'll try and help: [info@mobweb.ch](mailto:info@mobweb.ch).