<?php namespace XoopsModules\Smartmedia;

use XoopsModules\Smartmedia;

/**
 * SmartMedia Content class
 *
 * @author  marcan <marcan@smartfactory.ca>
 * @access  public
 * @package SmartMedia
 */
class Content extends \XoopsObject
{
    public $languageid;

    public $content_text = null;

    public $_created_languages = null;

    public $_canAddLanguage = null;

    /**
     * @param string $languageid
     * @param null   $id
     */
    public function __construct($languageid = 'default', $id = null)
    {
        $smartConfig =& smartmedia_getModuleConfig();

        $this->initVar('id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('language_id', XOBJ_DTYPE_TXTBOX, null, true, 50);
        $this->initVar('module_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('item_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('item_type', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('value', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('created_date', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('created_uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('modified_date', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('modified_uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('version', XOBJ_DTYPE_INT, 0, false);

        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            }
        } else {
            $this->setNew();
        }
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return ($this->contentid() == -1);
    }

    /**
     * @return mixed
     */
    public function contentid()
    {
        return $this->getVar('contentid');
    }

    /**
     * @return mixed
     */
    public function parentid()
    {
        return $this->getVar('parentid');
    }

    /**
     * @return mixed
     */
    public function weight()
    {
        return $this->getVar('weight');
    }

    /**
     * @param  string $format
     * @return mixed|string
     */
    public function image($format = 'S')
    {
        if ('' != $this->getVar('image')) {
            return $this->getVar('image', $format);
        } else {
            return 'blank.png';
        }
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function default_languageid($format = 'S')
    {
        return $this->getVar('default_languageid', $format);
    }

    // Functions to retreive text info

    /**
     * @param  string $format
     * @return mixed
     */
    public function title($format = 'S')
    {
        $myts = \MyTextSanitizer::getInstance();
        if (('s' === strtolower($format)) || ('show' === strtolower($format))) {
            return $myts->undoHtmlSpecialChars($this->content_text->getVar('title', 'e'), 1);
        } elseif ('clean' === strtolower($format)) {
            return smartmedia_metagen_html2text($myts->undoHtmlSpecialChars($this->content_text->getVar('title')));
        } else {
            return $this->content_text->getVar('title', $format);
        }
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function description($format = 'S')
    {
        return $this->content_text->getVar('description', $format);
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function meta_description($format = 'S')
    {
        return $this->content_text->getVar('meta_description', $format);
    }

    // Functions to save text info

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setTextVar($key, $value)
    {
        return $this->content_text->setVar($key, $value);
    }

    /**
     * @return string
     */
    public function getItemUrl()
    {
        return SMARTMEDIA_URL . 'content.php?contentid=' . $this->contentid();
    }

    /**
     * @return string
     */
    public function getItemLink()
    {
        return "<a href='" . $this->getItemUrl() . "'>" . $this->title() . '</a>';
    }

    /**
     * @param  bool $force
     * @return bool
     */
    public function store($force = true)
    {
        global $smartmedia_contentHandler;
        if (!$smartmedia_contentHandler->insert($this, $force)) {
            return false;
        }
        $this->content_text->setVar('contentid', $this->contentid());

        return $this->content_text->store();
    }

    /**
     * @param  bool $exceptDefault
     * @return mixed
     */
    public function getAllLanguages($exceptDefault = false)
    {
        global $smartmedia_content_textHandler;
        $criteria = new    \CriteriaCompo();
        $criteria->add(new \Criteria('contentid', $this->contentid()));
        if ($exceptDefault) {
            $criteria->add(new \Criteria('languageid', $this->default_languageid(), '<>'));
        }

        return $smartmedia_content_textHandler->getObjects($criteria);
    }

    /**
     * @return null
     */
    public function getCreatedLanguages()
    {
        if (null != $this->_created_languages) {
            return $this->_created_languages;
        }
        global $smartmedia_content_textHandler;
        $this->_created_languages = $smartmedia_content_textHandler->getCreatedLanguages($this->contentid());

        return $this->_created_languages;
    }

    /**
     * @return bool|null
     */
    public function canAddLanguage()
    {
        if (null != $this->_canAddLanguage) {
            return $this->_canAddLanguage;
        }

        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $languageList     = \XoopsLists::getLangList();
        $createdLanguages = $this->getCreatedLanguages();

        $this->_canAddLanguage = (count($languageList) > count($createdLanguages));

        return $this->_canAddLanguage;
    }

    /**
     * @return string
     */
    public function adminLinks()
    {
        global $is_smartmedia_admin;
        global $xoopsModule;
        $pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
        if ($is_smartmedia_admin) {
            $ret = '';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/content.php?op=mod&contentid=' . $this->contentid() . '"><img src="' . $pathIcon16 . '/edit.png' . '" alt="' . _MD_SMARTMEDIA_CATEGORY_EDIT . '" title="' . _MD_SMARTMEDIA_CATEGORY_EDIT . '"/></a>';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/content.php?op=del&contentid=' . $this->contentid() . '"><img src="' . $pathIcon16 . '/delete.png' . '" alt="' . _MD_SMARTMEDIA_CATEGORY_DELETE . '" title="' . _MD_SMARTMEDIA_CATEGORY_DELETE . '"/></a>';

            return $ret;
        } else {
            return '';
        }
    }

    /**
     * @param  array $content
     * @return array
     */
    public function toArray($content = [])
    {
        $content['contentid'] = $this->contentid();
        $content['itemurl']   = $this->getItemUrl();
        $content['itemlink']  = $this->getItemLink();
        $content['parentid']  = $this->parentid();
        $content['weight']    = $this->weight();
        if ('blank.png' !== $this->getVar('image')) {
            $content['image_path'] = smartmedia_getImageDir('content', false) . $this->image();
        } else {
            $content['image_path'] = false;
        }
        $smartConfig                 =& smartmedia_getModuleConfig();
        $content['main_image_width'] = $smartConfig['main_image_width'];
        $content['list_image_width'] = $smartConfig['list_image_width'];
        $content['adminLinks']       = $this->adminLinks();

        $content['title']            = $this->title();
        $content['clean_title']      = $content['title'];
        $content['description']      = $this->description();
        $content['meta_description'] = $this->meta_description();

        // Hightlighting searched words
        $highlight = true;
        if ($highlight && isset($_GET['keywords'])) {
            $myts                   = \MyTextSanitizer::getInstance();
            $keywords               = $myts->htmlSpecialChars(trim(urldecode($_GET['keywords'])));
            $h                      = new KeyHighlighter($keywords, true, 'smartmedia_highlighter');
            $content['title']       = $h->highlight($content['title']);
            $content['description'] = $h->highlight($content['description']);
        }

        return $content;
    }

    /**
     * @return bool
     */
    public function hasChild()
    {
        $smartmediaFolderHandler = Smartmedia\Helper::getInstance()->getHandler('Folder');
        $count                   = $smartmediaFolderHandler->getCountsByParent($this->contentid());
        if (isset($count[$this->contentid()]) && ($count[$this->contentid()] > 0)) {
            return true;
        } else {
            return false;
        }
    }
}
