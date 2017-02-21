<?php
namespace Beaded;

class Paginator {
    public $curr_page; 
    
    public $total_page;
    
    public $pagination_len;
    
    public $url_pattern;
    
    public $html;
    
    function __construct($curr_page = 1, $total_page, $url_pattern) {
        $this->curr_page = $curr_page; 
        
        $this->total_page = $total_page;
        
        $this->pagination_len = 5;
        
        $this->html = '';
        
        $this->url_pattern = $url_pattern;
    }
    
    static function create($curr_page, $total_page, $url_pattern) {
        $instance = new self($curr_page, $total_page, $url_pattern);
        
        return $instance->paginate();
    }
    
    function paginate() {
        if($this->total_page <= 1) {
            return '';
        }
        
        $is_gt_start_dist   = $this->curr_page >= $this->pagination_len;
        $is_gt_end_dist     = $this->total_page - $this->curr_page > $this->pagination_len-1;
        
        $this->html = '<div class="pagination">';
        $this->html .= '<ul>';
        $this->html .= $this->getPagingPrevHtml();
        
        $this->html .= $this->getItemHtmlForPage(1);
        
        if($this->total_page <= $this->pagination_len) {
            for($i = 2; $i < $this->total_page; $i++) {
                $this->html .= $this->getItemHtmlForPage($i);
            }
        } else {           
            if($is_gt_start_dist) {
                $this->html .= $this->getPagingDotsHtml();
            } else {
                // loop from 2 to page 5
                for($i = 2; $i <= $this->pagination_len; $i++) {
                    $this->html .= $this->getItemHtmlForPage($i);
                }
            }
            
            if($is_gt_start_dist && $is_gt_end_dist) {
                for($i = $this->curr_page-2; $i <= $this->curr_page+2; $i++) {
                    $this->html .= $this->getItemHtmlForPage($i);
                }
            }            
            
            if($is_gt_end_dist) {
                $this->html .= $this->getPagingDotsHtml();
            } else {
                // loop from curr page to total_page-1
                for($i = $this->total_page-($this->pagination_len-1); $i < $this->total_page; $i++) {
                    $this->html .= $this->getItemHtmlForPage($i);
                }
            }
        } 
        
        $this->html .= $this->getItemHtmlForPage($this->total_page);
        $this->html .= $this->getPagingNextHtml();
        $this->html .= '</ul>';
        $this->html .= '</div>';
        
        return $this->html;
    }
    
    private function getItemHtmlForPage($page) {
        if($this->curr_page == $page) {
            return '<li><span class="paging-item paging-current-item">'.$page.'</span></li>';
        } 
        
        return '<li><a class="paging-item paging-link" href="'.$this->getPageUrl($page).'">'.$page.'</a></li>';
    }
    
    private function getPagingDotsHtml() {
        return '<li><span class="paging-item paging-dots">...</span></li>';
    }
    
    private function getPagingPrevHtml() {
        if($this->curr_page > 1) {
            return '<li><a class="paging-item paging-prev paging-link" href="'.$this->getPageUrl($this->curr_page-1).'">&laquo; prev</a></li>';
        }
        
        return '';
    }
    
    private function getPagingNextHtml() {
        if($this->curr_page < $this->total_page) {
            return '<li><a class="paging-item paging-next paging-link" href="'.$this->getPageUrl($this->curr_page+1).'">next &raquo;</a></li>';
        }
        
        return '';
    }
    
    /**
     * Finds the %d and replace it with the page number
     * @param int $page
     * @return string
     */
    private function getPageUrl($page) {
        return sprintf($this->url_pattern, $page);
    }
}
