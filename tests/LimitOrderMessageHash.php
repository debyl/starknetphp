<?php
use starknetphp\Starknet;

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
//"party_a_order": {
//	"message_hash": "0x397e76d1667c4454bfb83514e120583af836f8e32a516765497823eabe16a3f",
//	"private_key": "0x3c1e9550e66958296d11b60f8e8e7a7ad990d07fa65d5f7652c4a6c87d4e3cc"
//},
//"signature": {
//	"r": "0x173fd03d8b008ee7432977ac27d1e9d1a1f6c98b1a2f05fa84a21c84c44e882",
//	"s": "0x4b6d75385aed025aa222f28a0adc6d58db78ff17e51c3f59e259b131cd5a1cc"
//},


$party_b_order = [
	'vaultIdSell' => 221,
	'vaultIdBuy' => 227,
	'amountBuy' => '21546867497489',
	'amountSell' => '14702421154895',
	'tokenBuy' => '0x5fa3383597691ea9d827a79e1a4f0f7989c35ced18ca9619de8ab97e661020',
	'tokenSell' => '0x774961c824a3b0fb3d2965f01471c9c7734bf8dbde659e0c08dca2ef18d56a',
	'nonce' => 1,
	'expirationTimestamp' => 468963
];
//party_b_order
//"party_b_order": {
//	"message_hash": "0x6adb14408452ede28b89f40ca1847eca4de6a2dd6eb2c7d6dc5584f9399586",
//	"private_key": "0x4c1e9550e66958296d11b60f8e8e7a7ad990d07fa65d5f7652c4a6c87d4e3cc"
//},
//"signature": {
//	"r": "0x2ee2b8927122f93dd5fc07a11980f0fab4c8358e5d1306bfee5e095355d2ad0",
//	"s": "0x64d393473af2ebab736c579ad511bf439263e4740f9ad299498bda2e75b0e9"
//},


$party_a = new Starknet('0x3c1e9550e66958296d11b60f8e8e7a7ad990d07fa65d5f7652c4a6c87d4e3cc', '0x77a3b314db07c45076d11f62b6f9e748a39790441823307743cf00d6597ea43');
print 'Party A Limit Order Message Hash: "' . $party_a->hashLimitOrder( $party_a_order ) . '"';
$party_b = new Starknet('0x4c1e9550e66958296d11b60f8e8e7a7ad990d07fa65d5f7652c4a6c87d4e3cc', '0x3d8a9687c613b2be32b55c5c0460e012b592e2fbbb4fc281fb87b0d8c441b3e');
print 'Party B Limit Order Message Hash: "' . $party_b->hashLimitOrder( $party_b_order ) . '"';
