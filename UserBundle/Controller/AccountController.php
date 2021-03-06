<?php

/*
 * This file is part of the CCDN UserBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNUser\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

//use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserInterface;

/*
 * Deals with routes:
 * /account
 * /account/edit
 *
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class AccountController extends ContainerAware
{
	

	/**
	 *
	 * @access public
	 * @param int $user_id
	 * @return RedirectResponse|RenderResponse
	 */
    public function showAction($user_id)
    {
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
		
		if (!$user_id || $user_id == 0)
		{
			$user = $this->container->get('security.context')->getToken()->getUser();
		} else {
			$user = $this->container->get('user.repository')->findOneById($user_id);			
		}

		if ( ! is_object($user) || ! $user instanceof UserInterface)
		{
            throw new AccessDeniedException('You do not have access to this section.');
        }
		
		if ($user->getId() == $this->container->get('security.context')->getToken()->getUser()->getId()
		|| $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')
		|| $this->container->get('security.context')->isGranted('ROLE_ADMIN')
		|| $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
		} else {
			throw new AccessDeniedException('You do not have access to this section.');
		}
		
        return $this->container->get('templating')->renderResponse('CCDNUserUserBundle:Account:show.html.'.$this->container->getParameter('fos_user.template.engine'), array('user' => $user));
    }


	/**
	 *
	 * @access public
	 * @param int $user_id
	 * @return RedirectResponse|RenderResponse
	 */
    public function editAction($user_id)
    {
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
			
		if ( ! $user_id || $user_id == 0)
		{
			$user = $this->container->get('security.context')->getToken()->getUser();
		} else {
			$user = $this->container->get('user.repository')->findOneById($user_id);			
		}
        
		if ( ! is_object($user) || ! $user instanceof UserInterface)
		{
            throw new AccessDeniedException('You do not have access to this section.');
        }

		if ($user->getId() == $this->container->get('security.context')->getToken()->getUser()->getId()
		|| $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')
		|| $this->container->get('security.context')->isGranted('ROLE_ADMIN')
		|| $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			
		} else {
		    throw new AccessDeniedException('You do not have access to this section.');
		}
		
/*		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
		
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('You do not have access to this section.');
        }*/

        $form = $this->container->get('fos_user.profile.form');
        $formHandler = $this->container->get('fos_user.profile.form.handler');

        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'flash.account.updated');

            return new RedirectResponse($this->container->get('router')->generate('cc_user_account_show_by_id', array('user_id' => $user_id)));
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Account:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView(), 'theme' => $this->container->getParameter('fos_user.template.theme'))
        );
    }


	/**
	 *
	 * @access protected
	 * @return string
	 */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }
	
}