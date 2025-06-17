<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCategoryModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'categories';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'parent_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'lft', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'rgt', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $i18n = array('name', 'description');
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
	
	public function getNode($locale_id, $id=null)
	{
		$tree = array();
	    
	    if (!is_null($id))
	    {
	    	$this->where('t1.id', $id);
	    }
	    $arr = $this->limit(1)->findAll()->getData();
	    if (count($arr) === 1)
	    {
	    	
	    	$right = array();
	    	
	    	$descendants = $this->reset()
	    		->select('t1.*, t2.content AS `name`')
	    		->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '$locale_id' AND t2.field = 'name'", 'left outer')
	    		->where(sprintf("t1.lft BETWEEN '%u' AND '%u'", $arr[0]['lft'], $arr[0]['rgt']))
	    		->orderBy('t1.lft ASC')
	    		->findAll()
	    		->getData();
	    	
	    	foreach ($descendants as $descendant)
	    	{
		    	
		        if (count($right) > 0)
		        {
		            
		            while ($right[count($right) - 1] < $descendant['rgt'])
		            {
		                array_pop($right);
		            }
		        }
		        
		        $repeatAmount = count($right) - 1;
		        if($repeatAmount < 0)
		        {
		        	$repeatAmount = 0;
		        }
		        if ($descendant['id'] != $id)
		        {
		        	$tree[] = array(
		        		'deep' => $repeatAmount,
		        		'children' => ($descendant['rgt'] - $descendant['lft'] - 1) / 2,
		        		'data' => $descendant
		        	);
		        }
		       
		        $right[] = $descendant['rgt'];
	    	}
	    	$siblings = array();
	    	foreach ($tree as $k => $v)
	    	{
	    		if (!isset($siblings[$v['deep']."|".$v['data']['parent_id']]))
	    		{
	    			$siblings[$v['deep']."|".$v['data']['parent_id']] = 0;
	    		}
	    		$siblings[$v['deep']."|".$v['data']['parent_id']] += 1;
	    	}
	    	
	    	foreach ($tree as $k => $v)
	    	{
	    		$tree[$k]['siblings'] = isset($siblings[$v['deep']."|".$v['data']['parent_id']]) ? $siblings[$v['deep']."|".$v['data']['parent_id']] : 0;
	    	}
	    }
	    return $tree;
	}

	public function rebuildTree($parent_id, $left)
	{
       
        $right = $left + 1;
        
        $arr = $this->reset()->where('t1.parent_id', $parent_id)->orderBy('t1.lft ASC')->findAll()->getDataPair('id', 'id');
        foreach ($arr as $id)
        {
        	
        	$right = $this->rebuildTree($id, $right);
        }
       
        $this->reset()->set('id', $parent_id)->modify(array('lft' => $left, 'rgt' => $right));
       
        return $right + 1;
    }

	public function saveNode($data, $parent_id)
	{
		if ((int) $parent_id > 0)
		{
			$parent = $this->reset()->find($parent_id)->getData();
			if (count($parent) > 0)
			{
				$_lft = $parent['rgt'];
				$_rgt = $_lft + 1;
				$rgt = $parent['rgt'] - 1;
			} else {
				
				return -1;
			}
		} else {
			$_lft = 1;
			$_rgt = 2;
		}

		$data = array_merge($data, array('lft' => $_lft, 'rgt' => $_rgt));
		$id = $this->reset()->setAttributes($data)->insert()->getInsertId();
		if ($id !== false && (int) $id > 0 && isset($rgt))
		{
			$this
				->reset()
					->where('id !=', $id)
					->where('rgt >', $rgt)
					->modifyAll(array('rgt' => ':rgt + 2'))
				->reset()
					->where('id !=', $id)
					->where('lft >', $rgt)
					->modifyAll(array('lft' => ':lft + 2'));
		}
		$this->rebuildTree(1, 1);
		return $id;
	}
	
	public function updateNode($data)
	{
		$this->reset()->set('id', $data['id'])->modify($data);
		$this->rebuildTree(1, 1);
	}

	public function deleteNode($node_id, $node=null)
	{
		if (is_null($node))
		{
			$node = $this->reset()->find($node_id)->getData();
		}
		if (count($node) > 0)
		{
			$rgt = $node['rgt'];
		} else {
			
			return -1;
		}
		
		
		$children = $this->reset()->where('t1.parent_id', $node_id)->findAll()->getData();
		foreach ($children as $child)
		{
			$this->deleteNode($child['id'], $child);
		}

		
		$result = $this->reset()->set('id', $node_id)->erase()->getAffectedRows();
		if ((int) $result > 0)
		{
			$this
				->reset()
					->where('rgt >', $rgt)
					->modifyAll(array('rgt' => ':rgt - 2'))
				->reset()
					->where('lft >', $rgt)
					->modifyAll(array('lft' => ':lft - 2'));
		}
		return $result;
	}
}
?>