<?php

class db_coins extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'coins';
	}

	public function rules()
	{
		return array(
			array('name', 'required'),
			array('symbol', 'required'),
			array('symbol', 'unique'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'symbol2'	=> 'Official Symbol',
			'auxpow'	=> 'AUX PoW',
			'dontsell'	=> 'Don\'t sell',
			'sellonbid'	=> 'Sell on Bid',
			'txfee'		=> 'Tx Fee',
			'program'	=> 'Process name',
			'conf_folder'	=> 'Conf. folder',
			'rpchost'	=> 'RPC Host',
			'rpcport'	=> 'RPC Port',
			'rpcuser'	=> 'RPC User',
			'rpcpasswd'	=> 'RPC Password',
			'serveruser'	=> 'Server user',
			'hassubmitblock'=> 'Has submitblock',
			'hasmasternodes'=> 'Masternodes',
			'market'	=> 'Preferred market',
			'rpcencoding'	=> 'RPC Type',
			'specifications'=> 'Notes'
		);
	}

	public function getSymbol_show()
	{
		if(!empty($this->symbol2))
			return $this->symbol2;
		else
			return $this->symbol;
	}


}

