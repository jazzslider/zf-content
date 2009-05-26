<?php

class Content_View_Filter_SafeHtml implements Zend_Filter_Interface
{
  protected $_allowed;

  public function __construct($allowed = 'p,a[href],strong,em,blockquote[cite],q[cite],abbr,acronym,cite,dfn,ul,ol,li,dl,dt,dd,del,ins,sub,sup')
  {
    $this->setAllowed($allowed);
  }

  public function getAllowed()
  {
    return $this->_allowed;
  }

  public function setAllowed($allowed)
  {
    $this->_allowed = $allowed;
    return $this;
  }

  public function filter($value)
  {
    $filterChain = new Zend_Filter();
    
    $htmlFilter = new Content_View_Filter_HtmlPurifier();
    $config = $htmlFilter->getConfig();
    $config->set('AutoFormat', 'AutoParagraph', true);
    $config->set('AutoFormat', 'Linkify', true);
    $allowed = $this->getAllowed();
    if (null !== $allowed) {
      $config->set('HTML', 'Allowed', $allowed);
    }
    $filterChain->addFilter($htmlFilter);

    $geshiFilter = new Content_View_Filter_Geshi();
    $filterChain->addFilter($geshiFilter);

    return $filterChain->filter($value);
  }
}
