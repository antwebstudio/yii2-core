<?php  
namespace ant\interfaces;

interface Expirable
{
	public function getIsExpired();

	public function markAsExpired();

	public function renew();
}