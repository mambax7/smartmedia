<?php

use XoopsModules\Smartmedia;
/** @var Smartmedia\Helper $helper */
$helper = Smartmedia\Helper::getInstance();

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param     $description
 * @param int $maxWords
 * @return string
 */

function smartmedia_createMetaDescription($description, $maxWords = 100)
{
    $myts = \MyTextSanitizer::getInstance();

    $words = [];
    $words = explode(' ', smartmedia_metagen_html2text($description));

    $ret       = '';
    $i         = 1;
    $wordCount = count($words);
    foreach ($words as $word) {
        $ret .= $word;
        if ($i < $wordCount) {
            $ret .= ' ';
        }
        ++$i;
    }

    return $ret;
}

/**
 * @param $text
 * @param $minChar
 * @return array
 */
function smartmedia_findMetaKeywords($text, $minChar)
{
    $myts = \MyTextSanitizer::getInstance();

    $keywords         = [];
    $originalKeywords = explode(' ', smartmedia_metagen_html2text($text));
    foreach ($originalKeywords as $originalKeyword) {
        $secondRoundKeywords = explode("'", $originalKeyword);
        foreach ($secondRoundKeywords as $secondRoundKeyword) {
            if (strlen($secondRoundKeyword) >= $minChar) {
                if (!in_array($secondRoundKeyword, $keywords)) {
                    $keywords[] = trim($secondRoundKeyword);
                }
            }
        }
    }

    return $keywords;
}

/**
 * @param        $title
 * @param string $categoryPath
 * @param string $description
 * @param int    $minChar
 */
function smartmedia_createMetaTags($title, $categoryPath = '', $description = '', $minChar = 4)
{
    global $xoopsTpl, $xoopsModule;
    /** @var Smartmedia\Helper $helper */
    $helper = Smartmedia\Helper::getInstance();
    $myts = \MyTextSanitizer::getInstance();

    $ret = '';

    $title = $myts->displayTarea($title);
    $title = $myts->undoHtmlSpecialChars($title);

    if (isset($categoryPath)) {
        $categoryPath = $myts->displayTarea($categoryPath);
        $categoryPath = $myts->undoHtmlSpecialChars($categoryPath);
    }

    // Creating Meta Keywords
    if (isset($title) && ('' != $title)) {
        $keywords = smartmedia_findMetaKeywords($title, $minChar);

        if (null !== ($helper->getModule()) && null !==($helper->getConfig('moduleMetaKeywords')) && '' != $helper->getConfig('moduleMetaKeywords')) {
            $moduleKeywords = explode(',', $helper->getConfig('moduleMetaKeywords'));
            foreach ($moduleKeywords as $moduleKeyword) {
                if (!in_array($moduleKeyword, $keywords)) {
                    $keywords[] = trim($moduleKeyword);
                }
            }
        }

        $keywordsCount = count($keywords);
        for ($i = 0; $i < $keywordsCount; ++$i) {
            $ret .= $keywords[$i];
            if ($i < $keywordsCount - 1) {
                $ret .= ', ';
            }
        }

        $xoopsTpl->assign('xoops_meta_keywords', $ret);
    }
    // Creating Meta Description
    if ('' != $description) {
        $xoopsTpl->assign('xoops_meta_description', smartmedia_createMetaDescription($description));
    }

    // Creating Page Title
    $moduleName = '';
    $titleTag   = [];

    if (isset($xoopsModule)) {
        $moduleName         = $myts->displayTarea($xoopsModule->name());
        $titleTag['module'] = $moduleName;
    }

    if (isset($title) && ('' != $title) && (strtoupper($title) != strtoupper($moduleName))) {
        $titleTag['title'] = $title;
    }

    if (isset($categoryPath) && ('' != $categoryPath)) {
        $titleTag['category'] = $categoryPath;
    }

    $ret = '';

    if (isset($titleTag['title']) && '' != $titleTag['title']) {
        $ret .= smartmedia_metagen_html2text($titleTag['title']);
    }

    if (isset($titleTag['category']) && '' != $titleTag['category']) {
        if ('' != $ret) {
            $ret .= ' - ';
        }
        $ret .= $titleTag['category'];
    }
    if (isset($titleTag['module']) && '' != $titleTag['module']) {
        if ('' != $ret) {
            $ret .= ' - ';
        }
        $ret .= $titleTag['module'];
    }
    $xoopsTpl->assign('xoops_pagetitle', $ret);
}
