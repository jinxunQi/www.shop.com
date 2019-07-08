<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016100200644439",

		//商户私钥
		'merchant_private_key' => "MIIEpQIBAAKCAQEAqsspIu+3qlIRHM0u/gAhXpbZ0+6CBct4kXyqYR+iWTF5WwoGP7yskOgZ3E9hQzgdhoc9FkrzIcFzUwFyTJedznpbhr9Y83IUkwE1kPzfqO7Ms/lGvmhtP7Dp6Y3pwgpYN5rReastQgGKKTz92efvjsOwEeE+dBwTS22xCOpAHGc3yAuwL1XW8igN1oUGRUZhm0nLW2M2x8kXhFQ2A2QdxzzDDCYMeI4F6yeQ+qfToYjhM9hpqtTdfcRKWk2rbShXzrCC4IGfIoFV1yztXztyY9b9mdxbKTjYh2FEL3105nSuHrqzhY63xofBA20AD0d6/0T2jFD9p/i5XWk6YQuXzQIDAQABAoIBAGqF2btTfd753nhzNMrw898WAPgguvG4TROYiH+ZBl3Q8JqqbulT2OFsEa0JyWTuW+Pq91uvFWNJT49GyK4ipicAw5MQRv9XeKOTToCGZ0zq27uoTso1QWZHBi1H+LKwVwMfSWU0MT8IzM4Zn05ITW5ieK8+KACs/g++c3tW9vN/HEBDYnr/rAG2bw97zAP9a7khrDuJxmEKE4PK7YQ0SmbaTNdvnBz2T7VOXsP13KDQF9fXzAIpSl8gOubObFeevDsVjHBjn1cavXww9Ot/atWWpZiPl5dV1sCiyXeSAGle+TbeVSwQAS9OFKjBAPMSz3+VEQuV7HrBVnrCPo30NSECgYEA0v9LTVf1HHPFVeGUI+kT9hvC9rIiLd0EsmRpd8ysaMi7WKHg25hXMgSbM03vXdagWSFoU/B5jPHYkMBl2FJalF8dTT+B1oRzaISIN05Rx0RplcJp9GSQKj/j55CDKjw4vPnHKh1JEv4nG40qGfAiOsY/mA3HHWAePCAISomFUzkCgYEAzziy10Fo0GFl0F09Uisi27ClF1q/UO+5gAzUsgGfAhpwu6Q+d6AVrgSin8jpoiybR5UUduRxMbetNSUUcPgzQZevRUyfqS9knAeQ61+0lmRnrhrXgQkhKvKPYTp0I2zKrz1kciASNEKBGfYEG6Nz7E1o5ZBzcQ7hUH1zasIbRTUCgYEAy0U5pXSEZfm+deNRVVs5aNML+6BXPzn7SWVlGg3rVzNzEYs6VjQoZL59SmrdnNilN9jwalV0tG1VxvYj58hHDUOotf/ltOV7TYjItgdUz5U/5xAaBWFQZfORO0hEpM7Jln1H+3GlMY/52xgfJEfsohTVtUYoukscNa5ibnSYJjkCgYEAyMy2cGQPvy1CTcCsNT1bj+sCbzkefr1t0amdScjMXmtkpk+nwJ+9ol0XgSJdUytKZPkPVY5UKctE6mS+BCJe+MaVrt9rL7SfF5s1UP/yrHErDskv3vgLzeQyEBNmixVW6qzg8r4lJcLz+0YsAORI5si0Rw/M7ddvemG2P9NMPakCgYEAh3Vcyr5VjxDayvlZuRnC+G1DmeR3k8oOCCkgC54BLDo4x5GJqnybT414QkVMDbm52sK9REIMNx/KqrXv4IitRT/AbM5ztbmw8gSh0K0Yr/3FblgcnC0VXGZ6bBUDSjnHw98QAVBpDbHTYCQsbhePsSbMQcxZwrg963srrkPG4nY=",
		
		//异步通知地址
		'notify_url' => "http://外网可访问网关地址/alipay.trade.page.pay-PHP-UTF-8/notify_url.php",
		
		//同步跳转
		'return_url' => "http://外网可访问网关地址/alipay.trade.page.pay-PHP-UTF-8/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关(沙箱测试加上dev)
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlwyk05iOPgKdeQK6YsF3Y4wRw0bD7UikJi48scV7l8BJb8P9FcUvBwW3koiNcrNVDoci951bkw7RWQVXNMsuxHBIPWVdzS9oe+V4oIeZHnkzI3pEh2dveY9v05HjTKb9ZZo3s9PxrR4b5hfX+wwWU10AYoNt4BJUF1Tj+44Nr8c2ZM9fKRH9EHLRITyRTQ+ni1n4gOZS/7YmQuCWvKjFzHlTD8VxvLtGkTM2R/5XAhRv5zaBYw8sbYdNFeTyH3vw4WHSM5x0sIgWBAHwA+ldVils4+aP5M7V3FBzvP9BSB1qdNfihgOgBfxTnIyEVaqD/A7yyEOrs54TB33yWOWbzwIDAQAB",
);