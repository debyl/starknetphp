<h1 align="center">starknetphp</h1>
<h3 align="center">starknetphp allows you to interact with Starknet from a PHP application</h2>

<p align="center">
      <a href="https://starkware.co"><img alt="starkware" src="https://img.shields.io/badge/powered_by-StarkWare-navy"></a>
      <a href="https://github.com/Starknet-php/starknet.php/blob/main/LICENSE.md"><img alt="License" src="https://img.shields.io/badge/license-MIT-black"></a>
</p>

> This project is a work-in-progress. Code and documentation are currently under development and are subject to change

## Install

>  **Requires [PHP 8.0+](https://php.net/releases/)**

Install `starknetphp` via the [Composer](https://getcomposer.org/) package manager:

```bash
composer require debyl/starknetphp
```


## Usage

Test data from [starkex-resources](https://github.com/starkware-libs/starkex-resources/blob/master/crypto/starkware/crypto/signature/signature_test_data.json?fbclid=IwAR0a1cz5FLKyuNiqYXQdCIIHUAkfDc5KLdtonhZknj3mTWs1D6Hf4hgNJ0c)

The following code can be used with a public and private key to get signatures from Limit Order:
```bash
$party_a_order = [
    'vaultIdSell' => 21,
    'vaultIdBuy' => 27,
    'amountSell' => '2154686749748910716',
    'amountBuy' => '1470242115489520459',
    'tokenSell' => '0x5fa3383597691ea9d827a79e1a4f0f7989c35ced18ca9619de8ab97e661020',
    'tokenBuy' => '0x774961c824a3b0fb3d2965f01471c9c7734bf8dbde659e0c08dca2ef18d56a',
    'nonce' => 0,
    'expirationTimestamp' => 438953
];
$privateKey = '0x3c1e9550e66958296d11b60f8e8e7a7ad990d07fa65d5f7652c4a6c87d4e3cc'; // change with your private key
$publicKey  = '0x77a3b314db07c45076d11f62b6f9e748a39790441823307743cf00d6597ea43'; // change with your public key
$party_a = new Starknet($privateKey, $publicKey);
print_r($party_a->signLimitOrder( $party_a_order ));
```
The following code can be used with a public and private key to get message hash from Limit Order:
```bash
$party_a_order = [
    'vaultIdSell' => 21,
    'vaultIdBuy' => 27,
    'amountSell' => '2154686749748910716',
    'amountBuy' => '1470242115489520459',
    'tokenSell' => '0x5fa3383597691ea9d827a79e1a4f0f7989c35ced18ca9619de8ab97e661020',
    'tokenBuy' => '0x774961c824a3b0fb3d2965f01471c9c7734bf8dbde659e0c08dca2ef18d56a',
    'nonce' => 0,
    'expirationTimestamp' => 438953
];
$privateKey = '0x3c1e9550e66958296d11b60f8e8e7a7ad990d07fa65d5f7652c4a6c87d4e3cc'; // change with your private key
$publicKey  = '0x77a3b314db07c45076d11f62b6f9e748a39790441823307743cf00d6597ea43'; // change with your public key
$party_a = new Starknet($privateKey, $publicKey);
print $party_a->hashLimitOrder( $party_a_order );
```


## Security

If you discover any security related issues, please email cryptodebyl@gmail.com instead of using the issue tracker.

 
## Credits

-  [Crypto Debyl]


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


https://github.com/debyl
