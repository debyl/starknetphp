<?php
namespace starknetphp;

use BN\BN;
use starknetphp\Helpers\Constants;
use starknetphp\Helpers\Numbers;
use starknetphp\Helpers\Encode;
use starknetphp\Helpers\ellipticCurve;

class Starknet {
	private $starkKey_private;
	private $starkKey_public;
	
	private $ecPoints;
	
	private $zeroBn;
	private $twoPow22Bn;
	private $twoPow31Bn;
	private $twoPow63Bn;
	private $prime;
	private $maxEcdsaVal;
	
	public function __construct($privateKey, $publicKey) {
		$this->starkKey_private = $privateKey;
		$this->starkKey_public = $publicKey;
		$this->initialize();
	}

	private function initialize(){
		$this->zeroBn = new BN('0', 16);
		$this->twoPow22Bn = new BN('400000', 16);		
		$this->twoPow31Bn = new BN('80000000', 16);
		$this->twoPow63Bn = new BN('8000000000000000', 16);
		
		$this->prime = new BN( '800000000000011000000000000000000000000000000000000000000000001', 16 );
		$this->maxEcdsaVal = new BN( '800000000000000000000000000000000000000000000000000000000000000', 16 );
	}
	
	public function signLimitOrder( $limitOrder ){
		$messageHash = $this->hashLimitOrder( $limitOrder );
		$signature = ellipticCurve::sign($this->starkKey_private, $messageHash);
		$r = Encode::removeHexLeadingZero($signature[0]->toString(16));
		$s = Encode::removeHexLeadingZero($signature[1]->toString(16));
		return [ 'r' => $r, 's' => $s ];
	}

	public function hashMsg(
		$instructionTypeBn,
		$vault0Bn,
		$vault1Bn,
		$amount0Bn,
		$amount1Bn,
		$nonceBn,
		$expirationTimestampBn,
		$token0,
		$token1OrPubKey,
		$condition = null
	) {
		$packedMessage = $instructionTypeBn;
		$packedMessage = $packedMessage->ushln(31)->add($vault0Bn);
		$packedMessage = $packedMessage->ushln(31)->add($vault1Bn);
		$packedMessage = $packedMessage->ushln(63)->add($amount0Bn);
		$packedMessage = $packedMessage->ushln(63)->add($amount1Bn);
		$packedMessage = $packedMessage->ushln(31)->add($nonceBn);
		$packedMessage = $packedMessage->ushln(22)->add($expirationTimestampBn);
		
		$this->ecPoints = ellipticCurve::constantPoints();

		$msgHash = null;
		if ($condition === null) {
			$msgHash = $this->pedersen([$this->pedersen([$token0, $token1OrPubKey]), '0x'.$packedMessage->toString(16)]);
		} else {
			$msgHash = $this->pedersen([$this->pedersen([$this->pedersen([$token0, $token1OrPubKey]), $condition]),$packedMessage->toString(16)]);
		}
		$msgHashBN = new BN($msgHash, 16);
		$this->assertInRange($msgHashBN, $this->zeroBn, $this->maxEcdsaVal, 'msgHash');
		return $msgHash;
	}
	
	public function getLimitOrderMsgHash(
		$vaultSell,
		$vaultBuy,
		$amountSell,
		$amountBuy,
		$tokenSell,
		$tokenBuy,
		$nonce,
		$expirationTimestamp
	) {	
		if( Numbers::isHex($tokenSell)==0 || Numbers::isHex($tokenBuy)==0) exit('Hex strings expected to be prefixed with 0x.');
		
		$vaultSellBn = new BN($vaultSell);
		$vaultBuyBn = new BN($vaultBuy);
		$amountSellBn = new BN($amountSell, 10);
		$amountBuyBn = new BN($amountBuy, 10); 
		$tokenSellBn = new BN(Encode::removeHexPrefix($tokenSell), 16);
		$tokenBuyBn = new BN(Encode::removeHexPrefix($tokenBuy), 16);
		$nonceBn = new BN($nonce);
		$expirationTimestampBn = new BN($expirationTimestamp);
			
		$this->assertInRange($vaultSellBn, $this->zeroBn, $this->twoPow31Bn, '$vaultSellBn');
		$this->assertInRange($vaultBuyBn, $this->zeroBn, $this->twoPow31Bn, '$vaultBuyBn');
		$this->assertInRange($amountSellBn, $this->zeroBn, $this->twoPow63Bn, '$amountSellBn');
		$this->assertInRange($amountBuyBn, $this->zeroBn, $this->twoPow63Bn, '$amountBuyBn');
		$this->assertInRange($tokenSellBn, $this->zeroBn, $this->prime, '$tokenSellBn');
		$this->assertInRange($tokenBuyBn, $this->zeroBn, $this->prime, '$tokenBuyBn');
		$this->assertInRange($nonceBn, $this->zeroBn, $this->twoPow31Bn, '$nonceBn');
		$this->assertInRange($expirationTimestampBn, $this->zeroBn, $this->twoPow22Bn, '$expirationTimestampBn');

		$instructionType = $this->zeroBn;

		return $this->hashMsg(
			$instructionType,
			$vaultSellBn,
			$vaultBuyBn,
			$amountSellBn,
			$amountBuyBn,
			$nonceBn,
			$expirationTimestampBn,
			$tokenSell,
			$tokenBuy
		);
	}
	
	public function hashLimitOrder($limitOrder){
		$args = [
			$limitOrder['vaultIdSell'],
			$limitOrder['vaultIdBuy'],
			$limitOrder['amountSell'],
			$limitOrder['amountBuy'],
			$limitOrder['tokenSell'],
			$limitOrder['tokenBuy'],
			$limitOrder['nonce'],
			$limitOrder['expirationTimestamp']
		];
		return $this->getLimitOrderMsgHash(...$args);
	}	
	
	private function assertInRange($input, $lowerBound, $upperBound, $inputName = '') {
		$messageSuffix = $inputName === '' ? 'invalid length' : 'invalid '.$inputName.' length';
		if($input->gte($lowerBound)==0 || $input->lt($upperBound)==0){
			print "Message not signable, $messageSuffix.";
		}
 	}
	
	private function pedersen(array $dataArray){
		$point = $this->ecPoints[0];
		for ($i = 0; $i < sizeof($dataArray); $i++) {
			$x = Numbers::toBN($dataArray[$i]);
			if(($x->compare(Constants::ZERO()) > 0 || $x->equals(Constants::ZERO())) && $x->compare(Numbers::toBN(Encode::addHexPrefix(Constants::FIELD_PRIME))) < 0){
			// nist
			}else{
				print "Invalid input $x";
			}
			for ($j = 0; $j < 252; $j++) {
				$pt = $this->ecPoints[2 + $i * 252 + $j];
				assert(!$point->getX()->eq($pt->getX()));
				$val = (int) $x->bitwise_and(Constants::ONE())->toString();
				if ($val !== 0) $point = $point->add($pt);
				$x = $x->bitwise_rightShift(1);
			}
		}
		return Encode::removeHexLeadingZero($point->getX()->toString(16));
	}
	
	public function hasHexPrefix($str) {
		return substr($str, 0, 2) === '0x';
	}
}
