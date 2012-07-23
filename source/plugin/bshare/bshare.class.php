<?php
If(!defined('IN_DISCUZ')) {
	Exit('Access Deined');
}

class plugin_bshare {
    function plugin_bshare() {
        @include_once DISCUZ_ROOT.'/data/cache/bshare.show.inc';
        $this -> position = $cfg_show['position'];
        $this -> blog = $cfg_show['blog'];
    }
}

class plugin_bshare_group extends plugin_bshare {
    function viewthread_posttop() {
        if ($this -> position == 1) {
            return array('<div style="float:left;padding-top:5px;margin-bottom:15px;width:100%">' . bshare_script() . '</div>');
        } else {
            return array();
        }
    }
    
	function viewthread_postbottom() {
        if ($this -> position == 2) {
            return array('<div style=" padding-top:10px;overflow:hidden;">' . bshare_script() . '</div>');
        } else {
            return array('');
        }
    }
	
    function viewthread_useraction() {
        return '';
    }
}

class plugin_bshare_forum extends plugin_bshare {
    function viewthread_posttop() {
        if ($this -> position == 1) {
            return array('<div style="float:left;padding-top:5px;margin-bottom:15px;width:100%">' . bshare_script() . '</div>');
        } else {
            return array();
        }
    }
    
	function viewthread_postbottom() {
        if ($this -> position == 2) {
            return array('<div style=" padding-top:10px;overflow:hidden;">' . bshare_script() . '</div>');
        } else {
            return array('');
        }
    }
}

class plugin_bshare_home extends plugin_bshare {
    function space_blog_title() {
        if ($this -> blog == 1) {
            return '<div style=" padding-top:10px;">' . bshare_script() . '</div>';
        } else {
            return '';
        }
    }
    
	function space_blog_op_extra() {
        if ($this -> blog == 2) {
            return '<div style=" float:left;">' . bshare_script() . '</div>';
        } else {
            return '';
        }
    }
}

function bshare_script() {
    @include_once DISCUZ_ROOT . '/data/cache/bshare.inc';
    $reval = $cfg_bshare['styles'];
    return isset($reval) ? $reval : '';
}
?>

