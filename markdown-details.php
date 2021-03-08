<?php
namespace Grav\Plugin;

use \Grav\Common\Grav;
use \Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

class MarkdownDetailsPlugin extends Plugin
{
    protected $base_class;
    protected $title_class;
    protected $trigger_class;
    protected $content_class;
    protected $icon;
    protected $a11y;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0],
            'onTwigSiteVariables'   => ['onTwigSiteVariables', 0]
        ];
    }

    public function onMarkdownInitialized(Event $event)
    {
        $this->a11y = $this->config->get('plugins.markdown-details.a11y');
        $this->base_class  = $this->config->get('plugins.markdown-details.base_class');
        $this->title_class = $this->config->get('plugins.markdown-details.title_class');
        $this->trigger_class = $this->config->get('plugins.markdown-details.trigger_class');
        $this->content_class = $this->config->get('plugins.markdown-details.content_class');
        $this->icon = '<svg class="indicator" aria-hidden="true" focusable="false" viewBox="0 0 10 10"><rect class="indicator__vert" height="8" width="2" y="1" x="4"/><rect height="2" width="8" y="4" x="1"/></svg>';

        $markdown = $event['markdown'];

        $markdown->addBlockType('!', 'Details', true, false);

        $markdown->blockDetails = function($Line)
        {
            if (preg_match('/^!>\s?(\[(\w*)\]?)?\s*(.*)$/', $Line['text'], $matches))
            {
                $tag = ( $matches[2] ) ? $matches[2] : 'h2';
                $title = $matches[3];

                if ( $this->a11y )
                {
                    $Block = array(
                        'name' => 'opener',
                        'markup' => '<div class="' . $this->base_class . ' js-details" data-expanded="true"><' . $tag . ' class="' . $this->title_class . '"><button class="' . $this->trigger_class . '" aria-expanded="true">' . $title . '' . $this->icon . '</button></' . $tag . '><div class="' . $this->content_class . '">',
                    );
                }
                else {
                    $Block = array(
                        'name' => 'opener',
                        'markup' => '<details class="' . $this->base_class . '"><summary class="' . $this->trigger_class . '"><' . $tag . ' class="' . $this->title_class . '">' . $title . '</' . $tag . '>' . $this->icon . '</summary><div class="' . $this->content_class . '">',
                    );
                }

                return $Block;
            }

            if (preg_match('/^!@(.*)$/', $Line['text'], $matches))
            {
                if ( $this->a11y )
                {
                    $Block = array(
                        'name' => 'closer',
                        'markup' => '</div></div>'
                    );
                }
                else
                {
                    $Block = array(
                        'name' => 'closer',
                        'markup' => '</div></details>'
                    );
                }
                return $Block;
            }

        };
        $markdown->blockDetailsContinue = function($Line, array $Block)
        {
            if (preg_match('/^!@(.*)$/', $Line['text'], $matches))
            {
                if ( $this->a11y )
                {
                    $Block = array(
                        'name' => 'closer',
                        'markup' => '</div></div>'
                    );
                }
                else
                {
                    $Block = array(
                        'name' => 'closer',
                        'markup' => '</div></details>'
                    );
                }

                $Block['closed'] = true;
                return $Block;
            }
        };
    }

    public function onTwigSiteVariables()
    {
        if ($this->config->get('plugins.markdown-details.built_in_css'))
        {
            $this->grav['assets']
                ->add('plugin://markdown-details/assets/details.css');
        }

        if ($this->config->get('plugins.markdown-details.a11y');)
        {
            $this->grav['assets']->add('plugin://markdown-details/js/details.js', ['group' => 'bottom']);
        }
    }

}
