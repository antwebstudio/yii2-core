<?php

class Paging {
	protected $endOfPage = false;
	protected $itemPerPage;
	protected $totalItem;
	protected $currentPage = 1;
	
	public function __construct($totalItem, $itemPerPage = 10) {
		$this->totalItem = $totalItem;
		$this->itemPerPage = $itemPerPage;
	}
	
	public function getTotalPage() {
		return ceil($this->totalItem / $this->itemPerPage);
	}
	
	public function getPageSize() {
		return $this->itemPerPage;
	}
	
	public function getCurrentPage() {
		if ($this->endOfPage) return false;
		return $this->currentPage;
	}
	
	public function setPage($page) {
		if ($page > $this->getTotalPage()) {
			$this->endOfPage = true;
		} else {
			$this->endOfPage = false;
		}
		
		if ($page <= $this->getTotalPage() && $page >= 1) {
			$this->currentPage = $page;
		}
		return false;
	}
	
	public function nextPage($numberOfPage = 1) {
		return $this->setPage($this->currentPage + $numberOfPage);
	}
	
	public function prevPage($numberOfPage = 1) {
		return $this->setPage($this->currentPage - $numberOfPage);
	}
	
	public function getIsLastPage() {
		return $this->currentPage == $this->getTotalPage();
	}
	
	public function getIsFirstPage() {
		return $this->currentPage == 1;
	}
	
	public function getOffsetIndex() {
		return ($this->getCurrentPage() - 1) * $this->getPageSize();
	}
}