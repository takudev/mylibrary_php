<?php
/**
 * ページングクラス
 *
 */
class Pager {

    private $page = null;
    private $perPage = null;
    private $itemData = array();
    private $startIndex = 0;
    private $endIndex = 0;
    private $totalPage = 0;

    private $pageData = array();

    /**
     * コンストラクタ
     * ページに含めるデータを配列で指定します。
     *
     * @param array $itemData ページに含めるデータ
     */
    public function __construct(array $itemData, $per_page = 10){

        $this->itemData = $itemData;
        $this->perPage = $per_page;

        $this->initialize();

        $this->setCurrentPageNumber(1);
    }

    /**
     * 現在のページ番号を指定。
     * 有効範囲外のページ番号の場合は無視されます。このときユーザレベルの警告が発生します。
     *
     * @param int $page 現在のページ番号
     */
    public function setCurrentPageNumber($page)
    {
        // 指定したページ番号が有効範囲内なら保持
        if(0 < $page && $page <= $this->totalPage){
            $this->page = $page;
        }
        else{
            trigger_error("指定したページ番号[".$page."]は有効範囲外です。※ページ数：".$this->totalPage, E_USER_WARNING);
        }
    }

    /**
     * 1ページに含める件数を指定。
     * 指定しない場合は10です。
     *
     * @param int $per_page 1ページに含める件数
     */
    public function setPerPage($per_page)
    {
        $this->perPage = $per_page;

        $this->initialize();
    }

    /**
     * 現在のページ番号を取得
     *
     * @return int 現在のページ番号
     */
    public function getCurrentPageNumber()
    {
        return $this->page;
    }

    /**
     * 現在ページの次のページの番号を取得
     * 次のページがない場合は、現在のページ番号が返却されます。
     *
     * @return int 次のページ番号
     */
    public function getNextPageNumber()
    {
        $next_page = $this->page;

        if (!is_null($this->page) && $this->page < $this->totalPage) {
            $next_page = $this->page + 1;
        }

        return $next_page;
    }

    /**
     * 現在ページの前のページの番号を取得
     * 前のページがない場合は、現在のページ番号が返却されます。
     *
     * @return int 前のページ番号
     */
    public function getPrevPageNumber()
    {
        $prev_page = $this->page;

        if (!is_null($this->page) && 1 < $this->page) {
            $prev_page = $this->page - 1;
        }

        return $prev_page;
    }

    /**
     * 現在のページのデータを取得
     *
     * @return array 現在のページのデータ
     */
    public function getCurrentPageData()
    {
        if(!isset($this->pageData[$this->page - 1])){
            // メンバ変数pageがpageDataIndex外の値になることはありません。
            throw new Exception();
        }

        return $this->pageData[$this->page - 1];
    }

    /**
     * 現在のページの最初のIndex値を取得
     *
     * @return int 現在のページの最初のIndex
     */
    public function getCurrentPageStartIndex()
    {
        return $this->perPage * ($this->page - 1) + 1;
    }

    /**
     * 現在のページの最後のIndexを取得
     *
     * @return int 現在のページの最後のIndex
     */
    public function getCurrentPageEndIndex()
    {
        $last = $this->perPage * ($this->page);
        return (count($this->itemData) < $last) ? count($this->itemData) : $last;
    }

    /**
     * データの数を取得
     *
     * @return int データの数
     */
    public function getDataCount()
    {
        return count($this->itemData);
    }

    /**
     * ページの数を取得
     *
     * @return ページの数
     */
    public function getTotalPageCount()
    {
        return $this->totalPage;
    }

    /**
     * 次のページが存在するか
     *
     * @return boolean true  - 存在する
     *                 false - 存在しない
     */
    public function isNextPageExist()
    {
        if (!is_null($this->page) && $this->page < $this->totalPage) {
            return true;
        }

        return false;
    }

    /**
     * 前のページが存在するか
     *
     * @return boolean true  - 存在する
     *                 false - 存在しない
     */
    public function isPrevPageExist()
    {

        if (!is_null($this->page) && 1 < $this->page) {
            return true;
        }

        return false;
    }

    //=======================================================
    // private function
    //=======================================================
    /**
     * 各ページのデータを生成する
     */
    private function initialize()
    {
        for ($i = 0; $i < count($this->itemData); $i++) {
            $page_no = (int)($i / $this->perPage);
            $this->pageData[$page_no][] = $this->itemData[$i];
        }

        $this->totalPage = count($this->pageData);
    }
}

?>
