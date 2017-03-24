<?php

namespace supercool\supercooltools\controllers;

use supercool\supercooltools\SupercoolTools as SupercoolToolsPlugin;

use Craft;
use craft\web\Controller;

use craft\elements\Entry;
use craft\elements\Category;
use craft\helpers\Db;

class SupercoolToolsController extends Controller
{

	protected $allowAnonymous = true;

	/**
	 * Downloads a file and cleans up old temporary assets
	 */
	public function actionDownloadFile()
	{

		// Clean up temp assets files that are more than a day old
		$fileResults = array();

		$files = IOHelper::getFiles(craft()->path->getTempPath(), true);

		foreach ($files as $file)
		{
			$lastModifiedTime = IOHelper::getLastTimeModified($file, true);
			if (substr(IOHelper::getFileName($file, false, true), 0, 6) === "assets" && DateTimeHelper::currentTimeStamp() - $lastModifiedTime->getTimestamp() >= 86400)
			{
				IOHelper::deleteFile($file);
			}
		}

		// Sort out the file we want to download
		$id = craft()->request->getParam('id');

		$criteria = craft()->elements->getCriteria(ElementType::Asset);
		$criteria->id = $id;
		$asset = $criteria->first();

		if ($asset)
		{

			// Get a local copy of the file
			$sourceType = craft()->assetSources->getSourceTypeById($asset->sourceId);
			$localCopy = $sourceType->getLocalCopy($asset);

			// Send it to the browser
			craft()->request->sendFile($asset->filename, IOHelper::getFileContents($localCopy), array('forceDownload' => true));
			craft()->end();

		}

	}

	/**
	 * Clear the cache
	 */
	public function actionClearCache()
	{

		// Delete all the template caches!
		craft()->templateCache->deleteAllCaches();

		// Run any pending tasks
		if (!craft()->tasks->isTaskRunning())
		{
			// Is there a pending task?
			$task = craft()->tasks->getNextPendingTask();

			if ($task)
			{
				// Attempt to close the connection if this is an Ajax request
				if (craft()->request->isAjaxRequest())
				{
					craft()->request->close();
				}

				// Start running tasks
				craft()->tasks->runPendingTasks();
			}
		}

		// Exit
		craft()->end();

	}

	/**
	 * Fork of tags/searchForTags adjusted to cope with any element
	 */
	public function actionSearchForElements()
	{
		$this->requirePostRequest();
		$this->requireAcceptsJson();

		$request = Craft::$app->getRequest();

		$search = $request->getRequiredBodyParam('search');
		$excludeIds = $request->getRequiredBodyParam('excludeIds', array());

		// // Get the post data
		$elementType = $request->getRequiredBodyParam('elementType');
		$sources = $request->getRequiredBodyParam('sources');

		// Deal with Entries
		if ($elementType == "Entry")
		{

			// Fangle the sections out of the sources
			$sections = array();
			if (is_array($sources))
			{

				foreach ($sources as $source)
				{
					switch ($source)
					{
						case 'singles':
						{
							$sections = array_merge($sections, craft()->sections->getSectionsByType(SectionType::Single));
							break;
						}
						default:
						{
							if (preg_match('/^section:(\d+)$/', $source, $matches))
							{
								$section = craft()->sections->getSectionById($matches[1]);

								if ($section)
								{
									$sections = array_merge($sections, array($section));
								}
							}
						}
					}
				}

			}

			$criteria = Entry::find();
			$criteria->section = $sections;

		}
		// Deal with Categories
		else if ($elementType == "Category")
		{
			// Start the criteria
			$criteria = Category::find();
		}

		// Add and exclude ids
		$notIds = array('and');

		foreach ($excludeIds as $id)
		{
			$notIds[] = 'not '.$id;
		}

		// Set the rest of the criteria
		$criteria->title   = '*'.Db::escapeParam($search).'*';
		$criteria->id      = $notIds;
		$criteria->status  = null;
		$criteria->limit   = 20;
		$elements = $criteria->all();

		$return = array();
		$exactMatches = array();
		$exactMatch = false;

		$normalizedSearch = $search;

		foreach ($elements as $element)
		{
			if ($elementType == "Entry")
			{
				if (!is_array($sources))
				{
					$sourceKey = "*";
				}
				else if ($element->section->type == SectionType::Single)
				{
					$sourceKey = "singles";
				}
				else
				{
					$sourceKey = "section:".$element->section->id;
				}

				$return[$sourceKey][] = array(
					'id'          => $element->id,
					'title'       => $element->title,
					'status'      => $element->status,
					'sourceName'  => $element->section->name
				);
			}
			else if ($elementType == "Category")
			{
				$sourceKey = "group:".$element->group->id;
				$return[$sourceKey][] = array(
					'id'          => $element->id,
					'title'       => $element->title,
					'status'      => $element->status,
					'sourceName'  => $element->group->name
				);
			}

			$normalizedTitle = $element->title;

			if ($normalizedTitle == $normalizedSearch)
			{
				$exactMatches[] = 1;
				$exactMatch = true;
			}
			else
			{
				$exactMatches[] = 0;
			}
		}

		// NOTE: We’ve lost the sorting by exact match
		// array_multisort($exactMatches, SORT_DESC, $return);
		
		return $this->asJson([
			'elements'   => $return,
			'exactMatch' => $exactMatch
		]);
	}

}