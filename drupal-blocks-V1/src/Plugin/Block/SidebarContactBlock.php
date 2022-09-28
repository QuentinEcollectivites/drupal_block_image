<?php

namespace Drupal\lwblocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;

/**
 * Provides an contact block for sidebar.
 *
 * @Block(
 *   id = "lwblocks_contactblock",
 *   admin_label = @Translation("Contact Block"),
 *   category = @Translation("lwblocks")
 * )
 */

class SidebarContactBlock extends BlockBase implements BlockPluginInterface {
    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);
        $config = $this->getConfiguration();
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Titre'),
            '#default_value' => isset($config['title']) ? $config['title'] : '',
        ];
        $form['url'] = [
            '#type' => 'url',
            '#title' => $this->t('Lien à intégrer au bloc '),
            '#description' => $this->t('Lien'),
            '#default_value' => isset($config['url']) ? $config['url'] : '',
        ];
        $form['description'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Description'),
            '#default_value' => isset($config['description']) ? $config['description'] : '',
        ];
	$form['check'] = [
		'#type' => 'checkbox',
		'#title' => $this->t('Masquer le Lien user-friendly ?'),
		'#default_value' => isset($config['check']) ? $config['check'] : '',
	];
	$form['image'] = [
                '#type' => 'managed_file',
                '#title' => t('Icone à intégrer'),
                '#upload_validators' => array(
                        'file_validate_extensions' => array('gif png jpg jpeg'),
                        'file_validate_size' => array(85600000),
                ),
                '#theme' => 'image_widget',
	        '#preview_imgage_style' => 'medium',
                '#upload_location' => 'public://images',
                '#progress_message' => 'One moment while we save your file...',
                '#default_value' => isset($this->configuration['image']) ? $this->configuration['image'] : '',
                '#required' => TRUE,
        ];

        return $form;
    }
    public function blockSubmit($form, FormStateInterface $form_state) {
	$cardImage = $form_state->getValue('image');
	      if ($cardImage != $this->configuration['image']) {
        	  if (!empty($cardImage[0])) {
             		 $file = File::load($cardImage[0]);
             		 $file->setPermanent();
             		 $file->save;
          		}
      		}


        $this->setConfigurationValue('title', $form_state->getValue('title'));
        $this->setConfigurationValue('url', $form_state->getValue('url'));
        $this->setConfigurationValue('check', $form_state->getValue('check'));
	$this->setConfigurationValue('description', $form_state->getValue('description'));
	$this->setConfigurationValue('image', $form_state->getValue('image'));
    }
    public function build() {
        $config = $this->getConfiguration();
        if (!empty($config['title'])) {
            $res['title'] = $config['title'];
        }
        if (!empty($config['url'])) {
            $res['url'] = $config['url'];
        }
        if (!empty($config['description'])) {
            $res['description'] = $config['description'];
        }
	if (!empty($config['check'])) {
            $res['check'] = $config['check'];
        }
	if (!empty($config['image'])) {
            	$file = File::load($config['image'][0]);
		$res['image'] = \Drupal\image\Entity\ImageStyle::load('medium')->buildUrl($file->getFileUri());
        }
        return [
            '#theme' => 'sidebar-contact-block',
            '#result' => $res
        ];

    }

}
