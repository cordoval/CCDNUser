<?php

/*
 * This file is part of the CCDN UserAdminBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNUser\UserAdminBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use CCDNComponent\CommonBundle\Entity\Manager\EntityManagerInterface;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class RoleSetFormHandler
{
	
	
	/**
	 *
	 * @access protected
	 */
	protected $factory;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $container;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $request;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $manager;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $options;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $form;


	/**
	 *
	 * @access public
	 * @param FormFactory $factory, ContainerInterface $container, EntityManagerInterface $manager
	 */
	public function __construct(FormFactory $factory, ContainerInterface $container, EntityManagerInterface $manager)
	{
		$this->options = array();
		$this->factory = $factory;
		$this->container = $container;
		$this->manager = $manager;

		$this->request = $container->get('request');
	}
	
	
	/**
	 *
	 * @access public
	 * @param Array() $options
	 * @return $this
	 */
	public function setOptions(array $options = null )
	{
		$this->options = $options;
		
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @return bool
	 */
	public function process()
	{			
		$this->getForm();
		
		if ($this->request->getMethod() == 'POST')
		{
			$this->form->bindRequest($this->request);
		
			$formData = $this->form->getData();
	
			if ($this->form->isValid())
			{	
				$this->onSuccess($this->form->getData());
				
				return true;				
			}
		}

		return false;
	}
	
	
	/**
	 *
	 * @access public
	 * @return Form
	 */
	public function getForm()
	{
		if (!$this->form)
		{
			$roleType = $this->container->get('role.form.change.type');
			$roleType->setOptions(array('user' => $this->options['user']));
			$this->form = $this->factory->create($roleType);
		}
		
		return $this->form;
	}
	
	
	/**
	 *
	 * @access protected
	 * @param $entity
	 */
	protected function onSuccess($entity)
    {
		$user = $this->options['user'];
		$user->setRoles($entity->getRoles());
		
		$this->manager->update($user);
    }

}