<?php

/**
 * @file DocCentricWorkflowPlugin.inc.php
 *
 * Copyright (c) 2018 Simon Fraser University
 * Copyright (c) 2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class GoogleScholarPlugin
 *
 * @brief Embed Hypothes.is onto Unoconv-generated PDF conversions of OJS
 * submission files.
 */

import('lib.pkp.classes.plugins.GenericPlugin');

define('PDF_SUFFIX', '.pdf');

class DocCentricWorkflowPlugin extends GenericPlugin {
	/**
	 * Register the plugin, if enabled.
	 * @param $category string
	 * @param $path string
	 * @return boolean
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			if ($this->getEnabled()) {
				HookRegistry::register('LoadHandler', array($this, 'setupCallbackHandler'));
				HookRegistry::register('FileManager::downloadFile', array($this, 'downloadFile'));
				HookRegistry::register('LinkAction::construct', array($this, 'constructLinkActionHandler'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Add the link action constructor handler
	 * @param $hookName string
	 * @param $params array
	 */
	function constructLinkActionHandler($hookName, $params) {
		$linkAction =& $params[0];
		$request = $this->getRequest();
		$router = $request->getRouter();

		// We're only interested in component requests.
		if (!is_a($router, 'PKPComponentRouter')) return false;

		// We're only interested in certain link action IDs.
		if (!in_array($linkAction->_id, array(
			'downloadFile',
		))) return false;

		assert(is_a($linkAction->_actionRequest, 'PostAndRedirectAction'));

		// Add the suffix to force the download.
		$linkAction->_actionRequest->_url .= '&docCentricWorkflowComponent=reallyDownload';

		return false;
	}

	/**
	 * Add the Hypothesis handler
	 * @param $hookName string
	 * @param $params array
	 */
	function setupCallbackHandler($hookName, $params) {
		$page = $params[0];
		if ($this->getEnabled() && $page == 'hypothesisApi') {
			$this->import('pages/HypothesisApiHandler');
			define('HANDLER_CLASS', 'HypothesisApiHandler');
			return true;
		}
		return false;
	}

	/**
	 * Handle a document download, intervening as needed to provide document-centric workflow.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function downloadFile($hookName, $args) {
		$filePath =& $args[0];
		$mediaType =& $args[1];
		$inline =& $args[2];
		$result =& $args[3];
		$fileName =& $args[4];

		$request = Application::getRequest();

		// If we really want a download, let the download code proceed.
		switch ($request->getUserVar('docCentricWorkflowComponent')) {
			case 'reallyDownload': return false;
			case 'viewConversion':
				// If it's already a PDF, let the normal code serve the file.
				if (substr($filePath, -strlen(PDF_SUFFIX))==PDF_SUFFIX) return false;

				// Create/use a PDF conversion.
				$pdfFilePath = $this->_ensureConversion($filePath);
				if (!$pdfFilePath) fatalError('Could not convert document!');

				$filePath = $pdfFilePath;
				$mediaType = 'application/pdf';
				return false;
			default: // Serve PDF.js container
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->assign(array(
					'title' => 'Document Centric Workflow',
					'pluginUrl' => $request->getBaseUrl() . '/' . $this->getPluginPath(),
					'pdfUrl' => $_SERVER['REQUEST_URI'] . '&docCentricWorkflowComponent=viewConversion'
				));
				$templateMgr->display($this->getTemplateResource('display.tpl'));
		}
		exit();
	}

	/**
	 * Ensure a conversion exists for the specified file, creating it if necessary.
	 * @param $sourceFile string Path and filename of source document to convert to PDF.
	 * @return string|null Path and filename if conversion succeeded; NULL otherwise.
	 */
	private function _ensureConversion($sourceFile) {
		$pdfFilePath = substr($filePath, 0, -strlen(PDF_SUFFIX)) . PDF_SUFFIX;
		if (!file_exists($pdfFilePath)) {
			$convertCommand = Config::getVar('cli', 'pdf_converter');
			if (!$convertCommand) return null;

			$convertCommand = strtr($convertCommand, array(
				'{$sourcePath}' => escapeshellarg($sourceFile),
				'{$targetPath}' => escapeshellarg($pdfFilePath),
			));
			exec($convertCommand);
			if (!file_exists($pdfFilePath)) return null;
		}
		return $pdfFilePath;
	}

	/**
	 * Get the display name of this plugin
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.docCentricWorkflow.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.docCentricWorkflow.description');
	}
}
