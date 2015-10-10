<?php

namespace sndsgd\http\outbound\response;


abstract class TemplateResponse extends \sndsgd\http\outbound\Response
{
    /**
     * The relative path to the template to render
     *
     * @var string
     */
    protected $template;

    /**
     * @param string $template
     */
    public function setTemplate(/*string*/ $template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()/*: string*/
    {
        return $this->template;
    }

    /**
     * Render the template
     *
     * @return string
     */
    public function render()
    {
        $loader = new Twig_Loader_Filesystem(APP_DIR."/templates");
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate($this->template);
        return $template->render($this->getData());
    }
}
