<?php
/**
 Admin Page Framework v3.7.7b02 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AdminPageFramework_Form_View___SectionCaption extends AdminPageFramework_FrameworkUtility {
    public $aSectionset = array();
    public $iSectionIndex = null;
    public $aFieldsets = array();
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();
    public $aCallbacks = array();
    public $oMsg = null;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSectionset, $this->iSectionIndex, $this->aFieldsets, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg,);
        $this->aSectionset = $_aParameters[0];
        $this->iSectionIndex = $_aParameters[1];
        $this->aFieldsets = $_aParameters[2];
        $this->aSavedData = $_aParameters[3];
        $this->aFieldErrors = $_aParameters[4];
        $this->aFieldTypeDefinitions = $_aParameters[5];
        $this->aCallbacks = $_aParameters[6];
        $this->oMsg = $_aParameters[7];
    }
    public function get() {
        return $this->_getCaption($this->aSectionset, $this->iSectionIndex, $this->aFieldsets, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
    }
    private function _getCaption(array $aSectionset, $iSectionIndex, $aFieldsets, $aFieldErrors, $aFieldTypeDefinitions, $aCallbacks, $oMsg) {
        if (!$aSectionset['description'] && !$aSectionset['title']) {
            return "<caption class='admin-page-framework-section-caption' style='display:none;'></caption>";
        }
        $_oArgumentFormater = new AdminPageFramework_Form_Model___Format_CollapsibleSection($aSectionset['collapsible'], $aSectionset['title'], $aSectionset);
        $_abCollapsible = $_oArgumentFormater->get();
        $_oCollapsibleSectionTitle = new AdminPageFramework_Form_View___CollapsibleSectionTitle(array('title' => $this->getElement($_abCollapsible, 'title', $aSectionset['title']), 'tag' => 'h3', 'section_index' => $iSectionIndex, 'collapsible' => $_abCollapsible, 'container_type' => 'section', 'sectionset' => $aSectionset,), $aFieldsets, $this->aSavedData, $this->aFieldErrors, $aFieldTypeDefinitions, $oMsg, $aCallbacks);
        $_bShowTitle = empty($_abCollapsible) && !$aSectionset['section_tab_slug'];
        return "<caption " . $this->getAttributes(array('class' => 'admin-page-framework-section-caption', 'data-section_tab' => $aSectionset['section_tab_slug'],)) . ">" . $_oCollapsibleSectionTitle->get() . $this->getAOrB($_bShowTitle, $this->_getCaptionTitle($aSectionset, $iSectionIndex, $aFieldsets, $aFieldTypeDefinitions), '') . $this->_getCaptionDescription($aSectionset, $aCallbacks['section_head_output']) . $this->_getSectionError($aSectionset, $aFieldErrors) . "</caption>";
    }
    private function _getSectionError($aSectionset, $aFieldErrors) {
        $_sSectionID = $aSectionset['section_id'];
        $_sSectionError = isset($aFieldErrors[$_sSectionID]) && is_string($aFieldErrors[$_sSectionID]) ? $aFieldErrors[$_sSectionID] : '';
        return $_sSectionError ? "<div class='admin-page-framework-error'><span class='section-error'>* " . $_sSectionError . "</span></div>" : '';
    }
    private function _getCaptionTitle($aSectionset, $iSectionIndex, $aFieldsets, $aFieldTypeDefinitions) {
        $_oSectionTitle = new AdminPageFramework_Form_View___SectionTitle(array('title' => $aSectionset['title'], 'tag' => 'h3', 'section_index' => $iSectionIndex, 'sectionset' => $aSectionset,), $aFieldsets, $this->aSavedData, $this->aFieldErrors, $aFieldTypeDefinitions, $this->oMsg, $this->aCallbacks);
        return "<div " . $this->getAttributes(array('class' => 'admin-page-framework-section-title', 'style' => $this->getAOrB($this->_shouldShowCaptionTitle($aSectionset, $iSectionIndex), '', 'display: none;'),)) . ">" . $_oSectionTitle->get() . "</div>";
    }
    private function _getCaptionDescription($aSectionset, $hfSectionCallback) {
        if ($aSectionset['collapsible']) {
            return '';
        }
        if (!is_callable($hfSectionCallback)) {
            return '';
        }
        $_oSectionDescription = new AdminPageFramework_Form_View___Description($aSectionset['description'], 'admin-page-framework-section-description');
        return "<div class='admin-page-framework-section-description'>" . call_user_func_array($hfSectionCallback, array($_oSectionDescription->get(), $aSectionset)) . "</div>";
    }
    private function _shouldShowCaptionTitle($aSectionset, $iSectionIndex) {
        if (!$aSectionset['title']) {
            return false;
        }
        if ($aSectionset['collapsible']) {
            return false;
        }
        if ($aSectionset['section_tab_slug']) {
            return false;
        }
        if ($aSectionset['repeatable'] && $iSectionIndex != 0) {
            return false;
        }
        return true;
    }
}