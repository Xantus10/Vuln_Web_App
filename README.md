# Vuln Web App

A web application to interactively learn about web attacks and web security. The greatest feature of this app is the fact that you can exploit all the attacks against this web app itself!

Note: Developed in PHP

## Localisation

Localised into Czech and English

## Deploying locally

Would you like to use this app? No problem! Here are the necessary steps.

1. Clone this repository
2. Choose which localisation you wish to support
3. Install your web server (Apache / nginx / xampp) + install PHP
4. Deploy the app (Copy paste into the specific directory)
5. Now you should be able to access web over HTTP, but the website needs HTTPS, so provide a cerificate (you may generate a self-signed cert)
6. I recommend you create a redirect from HTTP to HTTPS for the best UX
7. For a certain functionality, you should enable development error echoing (xampp has it by default)
8. Done!

## Security concerns

Yes, the app is vulnerable and you do not want anyone taking over your server (I understand). Don't worry, There is not a single RCE vulnerability in this application (they are discussed, but are not implemented). Most of the attacks are purely web based, not even interacting with the server OS. The one exception to this is LFI. Therefore it is vital to run the application as unprivileged user, **NOT ROOT**, and have all the basic defenses in place (like proper permissions on ssh authorized_keys files, etc.).

## Xtended

**VulnXtended** is a standalone sequel to the Vuln web app. Now with no restrictions on security, so it features anything up to multiple RCE type vulnerabilities. Because of this, VulnXtended should be only ever deployed locally in an isolated environment, as the vulnerabilities in the app lead directly to full server control.

**I repeat, do not deploy the VulnXtended publicly. You WILL get hacked!**

(Note: VulnXtended is only in English)
