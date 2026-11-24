# Balbooa Gridbox 3.x Custom Payment Gateway

Starter plugin for building a custom payment gateway for **[Gridbox](https://www.balbooa.com/gridbox)**.

This repository contains a working sample gateway — **CustomPay** — that shows how to connect any payment provider to the Gridbox store. Use it as a starting point for your own gateway.

<br>

> **CustomPay is a placeholder provider.**
> Its API endpoints and signature check are examples. Replace them with your payment provider's API before using the plugin on a live site.

<br>
<br>

## 📖 Developer Guide

**Start here: [Payment Gateway Developer Guide](https://www.balbooa.com/docs/gridbox/developers/custom-payment-gateway)**

The guide explains everything you need to build and ship your own gateway:

* How the Gridbox payment flow works — from checkout to webhook
* `PaymentInterface` and `BasePayment` API reference
* How to register a gateway so it survives Gridbox updates
* Step-by-step code for every plugin file
* Webhook security rules and testing
* Troubleshooting and known limitations

<br>
<br>

## 📥 Installing

1. Download `plg_system_custompay.zip` from [Releases](../../releases).
2. In your Joomla administrator panel, go to **System → Install → Extensions** and upload the file.
3. Open **Gridbox → Store → Payment Methods** — CustomPay appears in the list.
4. Open it, enter your keys, choose **Sandbox** for testing, then save and publish.

<br>
<br>

## 📚 Resources

* **[Payment Gateway Developer Guide](https://www.balbooa.com/docs/gridbox/developers/custom-payment-gateway)** — full guide with API reference
* **[Balbooa Joomla Gridbox](https://www.balbooa.com/gridbox)** — official product page
* **[Contact Balbooa](https://www.balbooa.com/help/contacts)** — questions and support
