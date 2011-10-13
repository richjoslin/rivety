<?php

/*
	Class: Cts_Cache

	About: Author
		Rich Joslin

	About: License
		<http://communit.as/docs/license>
*/
class Cts_Cache {

	/* Group: Constructors */

	/* Group: Instance Methods */

	/*
		Function: save
	*/
	public static function save($data, $name, $tags = null) {
		$caches_model = new Caches();
		$cache_row = $caches_model->fetchRowArray($caches_model
			->select()
			->where('name = ?', $name)
		);
		if (empty($cache_row)) {
			$cache = array(
				'name' => $name,
				'data' => serialize($data),
				'created_on' => date(DB_DATETIME_FORMAT),
			);
			try {
				$cache_id = $caches_model->insert($cache);
			} catch(Exception $e) {
				$cache_id = $caches_model->update($cache, $caches_model->select()->where('name = ?', $cache['name']));
			}
		} else {
			$cache_id = $cache_row['id'];
		}
		if (!empty($tags) && is_array($tags)) {
			$cache_tags_model = new CacheTags();
			$caches_tags_model = new CachesTags();
			$tag_data = array();
			foreach ($tags as $tag) {
				$tag_row = $cache_tags_model->fetchRowArray($cache_tags_model
					->select()
					->where('tag = ?', $tag)
				);
				if (empty($tag_row)) {
					$tag_data = array(
						'tag' => $tag,
						'created_on' => date(DB_DATETIME_FORMAT),
					);
					$tag_id = $cache_tags_model->insert($tag_data);
				} else {
					$tag_id = $tag_row['id'];
				}
				$tag_join_row = $caches_tags_model->fetchRowArray($caches_tags_model
					->select()
					->where('tag_id = ?', $tag_id)
					->where('cache_id = ?', $cache_id)
				);
				if (empty($tag_join_row)) {
					$tag_join_data = array(
						'cache_id' => $cache_id,
						'tag_id' => $tag_id,
						'created_on' => date(DB_DATETIME_FORMAT),
					);
					$caches_tags_model->insert($tag_join_data);
				}
			}
		}
	}

	/*
		Function: load
	*/
	public static function load($name) {
		$caches_model = new Caches();
		$cache = $caches_model->fetchRowArray($caches_model->select()->where('name = ?', $name));
		if (!empty($cache) && array_key_exists('data', $cache)) {
			return unserialize($cache['data']);
		} else {
			return false;
		}
	}

	/*
		Function: removeByName
	*/
	public static function removeByName($name) {
		$caches_model = new Caches();
		$where = $caches_model->getAdapter()->quoteInto('name = ?', $name);
		$cache_id = $caches_model->fetchArray($caches_model->select()->where('name = ?', $name));
		$caches_model->delete($where);
		$caches_tags_model = new CachesTags();
		$where = $caches_tags_model->getAdapter()->quoteInto('cache_id = ?', $cache_id);
		$caches_tags_model->delete($where);
	}

	/*
		Function: removeByTags
	*/
	public static function removeByTags($tags) {
		// this is an AND search, which is tricky when searching a many-to-many dataset
		// we tried it with a view using GROUP CONCAT but that ends up being much slower than doing it in PHP
		$caches_model = new Caches();
		$cache_tags_model = new CacheTags();
		$caches_tags_model = new CachesTags();
		$tags_select = $cache_tags_model->select()->where('1 = 0');
		foreach ($tags as $tag) {
			$tags_select->orWhere('tag = ?', $tag);
		}
		$tags_rows = $cache_tags_model->fetchAllArray($tags_select);
		$tag_ids_in = array();
		$tags_join_select = $caches_tags_model->select()->where('1 = 0');
		foreach ($tags_rows as $tags_row) {
			$tag_ids_in[] = $tags_row['id'];
			$tags_join_select->orWhere('tag_id = ?', $tags_row['id']);
		}
		$tags_join_rows = $caches_tags_model->fetchAllArray($tags_join_select);
		$tag_joins_merged = array();
		foreach ($tags_join_rows as $tags_join_row) {
			$tag_joins_merged[$tags_join_row['cache_id']][] = $tags_join_row['tag_id'];
		}
		$delete_caches_where = 'id = 0';
		$delete_cache_joins_where = 'cache_id = 0';
		sort($tag_ids_in);
		foreach ($tag_joins_merged as $cache_id => $tag_ids) {
			sort($tag_ids);
			if ($tag_ids_in === $tag_ids) {
				$delete_caches_where .= ' or id = '.$cache_id;
				$delete_cache_joins_where .= ' or cache_id = '.$cache_id;
			}
		}
		$caches_model->delete($delete_caches_where);
		$caches_tags_model->delete($delete_cache_joins_where);
	}

	/*
		Function: clear
	*/
	public static function clear() {
		$caches_model = new Caches();
		$cache_tags_model = new CacheTags();
		$caches_tags_model = new CachesTags();
		$caches_model->delete();
		$cache_tags_model->delete();
		$caches_tags_model->delete();
	}

}
