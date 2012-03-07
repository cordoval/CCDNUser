<?php

/*
 * This file is part of the CCDN MemberBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNUser\MemberBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class MemberController extends ContainerAware
{


	/**
	 *
	 * @access public
	 * @param int $page 
	 * @return RedirectResponse|RenderResponse
	 */
	public function showAction($page)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
				
		$members_paginated = $this->container->get('ccdn_user_user.user.repository')->findAllPaginated();

		$members_per_page = $this->container->getParameter('ccdn_user_member.members_per_page');
		$members_paginated->setMaxPerPage($members_per_page);
		$members_paginated->setCurrentPage($page, false, true);
		
		$members = $members_paginated->getCurrentPageResults();
		
		return $this->container->get('templating')->renderResponse('CCDNUserMemberBundle:List:list.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_user_member.user.profile_route'),
			'pager_route' => 'cc_members_paginated',
			'pager' => $members_paginated,
			'members' => $members,
		));
	}
	
	
	/**
	 *
	 * @access public
	 * @param int $page, char $alpha
	 * @return RedirectResponse|RenderResponse
	 */
	public function showFilteredAction($page, $alpha)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$members_paginated = $this->container->get('ccdn_user_user.user.repository')->findAllFilteredPaginated($alpha);

		$members_per_page = $this->container->getParameter('ccdn_user_member.members_per_page');
		$members_paginated->setMaxPerPage($members_per_page);
		$members_paginated->setCurrentPage($page, false, true);
		
		$members = $members_paginated->getCurrentPageResults();
		
		return $this->container->get('templating')->renderResponse('CCDNUserMemberBundle:List:list.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_user_member.user.profile_route'),
			'pager_route' => 'cc_members_alpha_paginated',
			'pager' => $members_paginated,
			'members' => $members,
		));	
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

