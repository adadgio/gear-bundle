<?php

namespace Adadgio\GearBundle\NodeRed\Configuration;

class Flow
{
    private $file;
    private $index;
    private $config;
    private $params;
    private $templatesDir;
    private $templatesPath;
    private $tab;
    private $flow;

    /**
     * Class constructor.
     *
     * @param string Flow template file basename
     * @return void
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->params = array();
    }

    /**
     * Receives config, index and path used later in variabilisation
     * necessary to create the final flow from the base json template.
     *
     * @param  integer Flow index, each one is unique and incremental
     * @param  array The bundle configuration (di)
     * @param  string Flows template base directory
     * @return object \Flow
     */
    public function injectConfig($index, array $config, $templatesDir)
    {
        $this->tab = (null === $this->tab) ? $index : $this->tab;
        $this->index = $index;
        $this->config = $config;
        $this->templatesDir = $templatesDir;
        $this->templatePath = $templatesDir.'/'.$this->file;

        if (!is_file($this->templatePath)) {
            throw new \Exception(sprintf('Node red flow template path is incorrect (no file found) "%s"', $this->templatePath));
        }

        return $this;
    }

    /**
     * Set one variable parameter in the flow. Parses before config.
     *
     * @param string
     * @param object \Flow
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Set several variable parameters in the flow. Parsed before config.
     *
     * @param array
     * @param object \Flow
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get final flow name.
     *
     * @return array Flow
     */
    public function getFinalName()
    {
        return $this->tab.'.flow.json';
    }

    /**
     * Get final flow as array reprensentation.
     *
     * @return array
     */
    public function getArray()
    {
        return $this->flow;
    }

    /**
     * Get final flow as JSON reprensentation.
     *
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->flow, JSON_PRETTY_PRINT);
    }

    /**
     * Get flow json file basename
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->file;
    }

    /**
     * Get flow json file template server path.
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Parse the flow from the flow template file contents
     * and replace variable parameters and indexes.
     *
     * @return object \Flow
     */
    public function parseFlow()
    {
        $this->flow = json_decode(file_get_contents($this->templatePath), true);

        $this->flow = $this->replaceFlowIndexes($this->flow);
        $this->flow = $this->replaceFlowParams($this->flow); // before config ! params can have vars inside
        $this->flow = $this->replaceFlowConfig($this->flow);
        $this->flow = $this->replaceNodeIds($this->flow);

        $this->flow = $this->displaceCoordinates($this->flow);

        $this->flow = $this->attachToTab($this->flow);

        return $this;
    }

    /**
     * Set flow tab name.
     *
     * @param string Tab name
     * @return object \Flow
     */
    public function setTab(Tab $tab)
    {
        $this->tab = $tab;

        return $this;
    }

    /**
     * Add a tab node and attach nodes to it
     *
     * @param array Flow array representation
     * @return array Flow array representation
     * @todo Should maybe not be here but rather in the \FlowBuilder (probably...?)
     */
    private function attachToTab(array $flow)
    {
        if (null === $this->tab) { return $flow; }

        // add the tab
        array_unshift($flow, $this->tab->toArray());

        // replace the "z" property of all the nodes (they belong to the tab!)
        foreach ($flow as $i => $node) {
            if (!isset($node['z'])) { continue; }
            $flow[$i]['z'] = $this->tab->getId();
        }

        return $flow;
    }

    /**
     * Displace node "y" coordinates a bit depending on index.
     *
     * @param array Flow array representation
     * @return array Flow array representation
     */
    private function displaceCoordinates(array $flow)
    {
        foreach ($flow as $i => $node) {
            if (!isset($flow[$i]['x'])) { continue; }
            $flow[$i]['x'] = $flow[$i]['x'] + (10 * $this->index);
        }

        return $flow;
    }

    /**
     * Replace array flow variable indexes.
     *
     * @param array Flow
     * @return array Flow
     */
    private function replaceFlowIndexes(array $flow)
    {
        $keys = array('path', 'url');

        foreach ($flow as $i => $node) {
            foreach ($keys as $key) {
                if (!isset($node[$key])) { continue; }

                $flow[$i][$key] = str_replace('%index%', $this->index, $flow[$i][$key]);
            }
        }

        return $flow;
    }
    
    /**
     * Replace array flow variable parameters.
     *
     * @param array Flow
     * @return array Flow
     */
    private function replaceFlowParams(array $flow)
    {
        $keys = array_keys($this->params);

        foreach ($flow as $i => $node) {
            foreach ($keys as $key) {
                if (!isset($node[$key])) { continue; }

                foreach ($this->config['flows']['parameters'] as $name => $value) {
                    $flow[$i][$key] = str_replace('%'.$name.'%', $value, $flow[$i][$key]);
                }
            }
        }

        return $flow;
    }

    /**
     * Replace array flow variable parameters.
     *
     * @param array Flow
     * @return array Flow
     */
    private function replaceFlowConfig(array $flow)
    {
        $keys = array('path', 'url');

        foreach ($flow as $i => $node) {
            foreach ($keys as $key) {
                if (!isset($node[$key])) { continue; }

                foreach ($this->config['flows']['parameters'] as $name => $value) {
                    $flow[$i][$key] = str_replace('%'.$name.'%', $value, $flow[$i][$key]);
                }
            }
        }

        return $flow;
    }

    /**
     * Replace node ids to avoid conflicts when several
     * same templates are added to the dashboard.
     *
     * @param array Flow
     * @return array Flow
     */
    private function replaceNodeIds(array $flow)
    {
        foreach ($flow as $i => $node) {
            if (!isset($node['id'])) { continue; } // skip nodes without ids

            // regenerate a brand new id
            $newId = self::createNodeId();
            $oldId = $node['id'];

            // replace all connected wires
            $flow = $this->updateFlowWires($flow, $oldId, $newId);

            // finally replace the node id
            $flow[$i]['id'] = $newId;
        }

        return $flow;
    }

    /**
     * Replace all wires ids with a new id recursively.
     *
     * @param  array  The flow
     * @param  string Old id
     * @param  string New id
     * @return array The modified flow
     */
    private function updateFlowWires(array $flow, $oldId, $newId)
    {
        foreach ($flow as $i => $node) {
            if (!isset($node['wires'])) { continue; }

            foreach ($node['wires'] as $j => $wires) {

                foreach ($wires as $k => $wireId) {
                    if ($wireId === $oldId) {
                        $flow[$i]['wires'][$j][$k] = $newId;
                    }
                }
            }
        }

        return $flow;
    }

    /**
     * Create a node red style node Id
     *
     * @return string
     */
    private static function createNodeId()
    {
        $id = uniqid();
        $len = strlen($id);
        $half = floor($len/2);

        return substr($id, 0, $half).'.'.substr($id, ($len - $half));
    }
}
