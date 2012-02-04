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
class RoleController extends ContainerAware
{
    

	/**
	 *
	 * @access public
	 * @param int $user_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function setUserRolesAction($user_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$user = $this->container->get('user.repository')->findOneById($user_id);

		if ( ! is_object($user) || ! $user instanceof UserInterface)
		{
            throw new NotFoundHttpException('the user does not exist.');
        }
			
		$formHandler = $this->container->get('role.form.change.handler')->setOptions(array('user' => $user));

		if ($formHandler->process())
		{	
			$this->container->get('session')->setFlash('notice', 
				$this->container->get('translator')->trans('flash.user.set_roles.success', array('%username%' => $user->getUsername()), 'CCDNUserUserAdminBundle'));
			
						
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_user_show', array(
				'user_id' => $user->getId(),
			)));
		}
		else
		{
			$form = $formHandler->getForm();
				
	/*		$crumb_trail = $this->container->get('crumb_trail')
				->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), 
					$this->container->get('router')->generate('cc_forum_category_index'), "home")
				->add($category->getName(),	$this->container->get('router')->generate('cc_forum_category_show', array('category_id' => $category->getId())), "category");*/
				
			return $this->container->get('templating')->renderResponse('CCDNUserUserAdminBundle:Role:set_users_role.html.' . $this->getEngine(), array(
			//	'user_profile_route' => $this->container->getParameter('ccdn_forum_forum.user.profile_route'),
				'user' => $user,
			//	'crumbs' => $crumb_trail,
				'form' => $form->createView(),
			));
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
