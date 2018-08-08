<?php

/**
 * @file pages/HypothesisApiHandler.inc.php
 *
 * Copyright (c) 2018 Simon Fraser University
 * Copyright (c) 2018 John Willinsky
 * Distributed under the GNU GPL v2 or later. For full terms see the file docs/COPYING.
 *
 * @class HypothesisApiHandler
 * @brief Handle requests for Hypothesis API interactions.
 */

import('classes.handler.Handler');

class HypothesisApiHandler extends Handler {
	/**
	 * Fetch API information
	 * @param $args array
	 * @param $request Request
	 */
	function api($args, $request) {
		$context = $request->getContext();
		$op = $request->getRequestedOp();
		$plugin = PluginRegistry::getPlugin('generic', 'doccentricworkflow');
		$contextId = ($context == null) ? 0 : $context->getId();

		header('content-type: application/json');
		header('access-control-allow-origin: *');
		header('referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin');
		header('x-content-type-options: nosniff');
		header('x-xss-protection: 1; mode=block');
		echo json_encode(array(
			'links' => array(
				'profile' => array(
					'read' => array(
						'url' => 'https://hypothes.is/api/profile',
						'method' => 'GET',
						'desc' => 'Fetch the user\'s profile',
					),
					'update' => array(
						'url' => 'https://hypothes.is/api/profile',
						'method' => 'PATCH',
						'desc' => 'Update a user\'s preferences',
					),
				),
				'search' => array(
					'url' => $request->url(null, null, 'search'),
					'method' => 'GET',
					'desc' => 'Search for annotations',
				),
				'group' => array(
					'member' => array(
						'delete' => array(
							'url' => $request->url(null, null, 'deleteGroupMember'),
							'method' => 'DELETE',
							'desc' => 'Remove the current user from a group.',
						),
					),
				),
				'links' => array (
					'url' => $request->url(null, null, 'linkTemplates'),
					'method' => 'GET',
					'desc' => 'URL templates for generating URLs for HTML pages',
				),
				'profile_groups' => array(
					'read' => array(
						'url' => $request->url(null, null, 'groups'),
						'method' => 'GET',
						'desc' => 'Fetch the user\'s groups',
					),
				),
				'annotation' => array(
					'hide' => array(
						'url' => $request->url(null, null, 'hideAnnotation'),
						'method' => 'PUT',
						'desc' => 'Hide an annotation as a group moderator.',
					),
					'unhide' => array(
						'url' => $request->url(null, null, 'unhideAnnotation'),
						'method' => 'DELETE',
						'desc' => 'Unhide an annotation as a group moderator.',
					),
					'read' => array(
						'url' => $request->url(null, null, 'annotation'),
						'method' => 'GET',
						'desc' => 'Fetch an annotation',
					),
					'create' => array(
						'url' => $request->url(null, null, 'createAnnotation'),
						'method' => 'POST',
						'desc' => 'Create an annotation',
					),
					'update' => array(
						'url' => $request->url(null, null, 'updateAnnotation'),
						'method' => 'PATCH',
						'desc' => 'Update an annotation',
					),
					'flag' => array(
						'url' => $request->url(null, null, 'flagAnnotation'),
						'method' => 'PUT',
						'desc' => 'Flag an annotation for review.',
					),
					'delete' => array(
						'url' => $request->url(null, null, 'deleteAnnotation'),
						'method' => 'DELETE',
						'desc' => 'Delete an annotation',
					),
				),
			),
		));
	}

	/**
	 * Fetch link template
	 * @param $args array
	 * @param $request Request
	 */
	function linkTemplates($args, $request) {
		$context = $request->getContext();
		$op = $request->getRequestedOp();
		$plugin = PluginRegistry::getPlugin('generic', 'doccentricworkflow');
		$contextId = ($context == null) ? 0 : $context->getId();

		header('content-type: application/json');
		header('access-control-allow-origin: *');
		header('referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin');
		header('x-content-type-options: nosniff');
		header('x-xss-protection: 1; mode=block');
		echo json_encode(array(
			'account.settings' => 'https://hypothes.is/account/settings',
			'forgot-password' => 'https://hypothes.is/forgot-password',
			'groups.new' => 'https://hypothes.is/groups/new',
			'help' => 'https://hypothes.is/docs/help',
			'oauth.authorize' => 'https://hypothes.is/oauth/authorize',
			'oauth.revoke' => 'https://hypothes.is/oauth/revoke',
			'search.tag' => 'https://hypothes.is/search?q=tag:":tag"',
			'signup' => 'https://hypothes.is/signup',
			'user' => 'https://hypothes.is/u/:user',
		));
	}

	/**
	 * Search
	 * @param $args array
	 * @param $request Request
	 */
	function search($args, $request) {
		$context = $request->getContext();
		$op = $request->getRequestedOp();
		$plugin = PluginRegistry::getPlugin('generic', 'doccentricworkflow');
		$contextId = ($context == null) ? 0 : $context->getId();

		header('content-type: application/json');
		header('access-control-allow-origin: *');
		header('referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin');
		header('x-content-type-options: nosniff');
		header('x-xss-protection: 1; mode=block');
		echo json_encode(array(
		));
	}
}

?>
