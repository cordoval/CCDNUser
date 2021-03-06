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

namespace CCDNUser\UserAdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Model\UserInterface;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class BanController extends ContainerAware
{


	/**
	 *
	 * @access public
	 * @param int $page
	 * @return RedirectResponse|RenderResponse
	 */
	public function showBannedUsersAction($page)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
				
		$users_paginated = $this->container->get('user.repository')->findAllBannedPaginated();

		$users_per_page = $this->container->getParameter('ccdn_user_member.members_per_page');
		$users_paginated->setMaxPerPage($users_per_page);
		$users_paginated->setCurrentPage($page, false, true);
		
		$users = $users_paginated->getCurrentPageResults();
		
		return $this->container->get('templating')->renderResponse('CCDNUserUserAdminBundle:Ban:show_banned_users.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_user_member.user.profile_route'),
//			'pager_route' => 'cc_members_paginated',
			'pager' => $users_paginated,
			'users' => $users,
		));
	}
	
	
	/**
	 *
	 * @access public
	 * @param int $user_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function banUserAction($user_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$user = $this->container->get('user.repository')->findOneById($user_id);			

		if ( ! is_object($user) || ! $user instanceof UserInterface)
		{
            throw new NotFoundHttpException('the user does not exist.');
        }
		
		$this->container->get('user.manager')->ban($user)->flushNow();
		
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.user.ban.success', array('%username%' => $user->getUsername()), 'CCDNUserUserAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('cc_admin_user_show', array('user_id' => $user_id)));

	}
	
	
	/**
	 *
	 * @access public
	 * @param int $user_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function unbanUserAction($user_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$user = $this->container->get('user.repository')->findOneById($user_id);			

		if ( ! is_object($user) || ! $user instanceof UserInterface)
		{
            throw new NotFoundHttpException('the user does not exist.');
        }
		
		$this->container->get('user.manager')->unban($user)->flushNow();
		
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.user.unban.success', array('%username%' => $user->getUsername()), 'CCDNUserUserAdminBundle'));
		
		return new RedirectResponse($this->container->get('router')->generate('cc_admin_user_show', array('user_id' => $user_id)));
        
	}
	
	
	/**
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function banIPAction()
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
				
	}
	
	
	/**
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function banEMailAddressAction()
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
	}


	/**
	 *
	 * @access protected
	 * @return string
	 */
	protected function getEngine()
    {
        return $this->container->getParameter('ccdn_user_member.template.engine');
    }

}
