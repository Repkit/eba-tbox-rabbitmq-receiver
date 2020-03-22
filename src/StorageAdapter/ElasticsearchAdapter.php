<?php
namespace TBoxRabbitMQReceiver\StorageAdapter;

use TBoxRabbitMQReceiver\StorageAdapter\StorageAdapterInterface;


class ElasticsearchAdapter implements StorageAdapterInterface
{
    /**
     * @var array
     */
    protected $config;
    
    /**
     * @var \Elasticsearch\Client
     */
    protected $client;
    
    /**
     * @var string
     */
    protected $validationError;
    
    /**
     * @var string
     */
    protected $persistError;

    
    public function __construct(array $Config)
    {
        $this->config = $Config;
        
        if (empty($this->config['connection']['hosts'])) {
            throw new \Exception('Elasticsearch hosts missing from config');
        }
        if (empty($this->config['index_settings'])) {
            throw new \Exception('Elasticsearch index settings missing from config');
        }
        
        // create client
        $this->client = \Elasticsearch\ClientBuilder::create()
                ->setHosts($this->config['connection']['hosts'])
                ->build();
        
        // create index if not exists
        $params = ['index' => $this->config['index_settings']['index']];
        if (!$this->client->indices()->exists($params)) {
            $this->client->indices()->create($this->config['index_settings']);
        }
    }
    
    
    /**
     * Validate the message before persisting to storage
     * 
     * @param string $Message Json encoded data
     * @return boolean
     */
    public function validate($Message)
    {
        $this->validationError = '';
        
        $message = json_decode($Message, TRUE);
        
        if (!is_array($message)) {
            $this->validationError = 'Could not decode json message';
            return FALSE;
        }
        
        $mappings = $this->config['index_settings']['body']['mappings'];
        $properties = array_values($mappings)[0]['properties'];
        
        $input = array_keys($message);
        $expected = array_keys($properties);
        
        if (array_diff($input, $expected)) {
            $this->validationError = 'Unknown properties: ' . implode(', ', array_diff($input, $expected));
            return FALSE;
        }
        
        if (array_diff($expected, $input)) {
            $this->validationError = 'Properties expected but not received: ' . implode(', ', array_diff($expected, $input));
            return FALSE;
        }
        
        $typeFail = [];
        foreach ($properties as $property => $attributes) {
            $type = $attributes['type'];
            if ($type === 'integer' && !is_int($message[$property]) && !is_null($message[$property])) {
                $typeFail[] = $property;
            }
        }
        if ($typeFail) {
            $this->validationError = 'Properties must be of type integer: ' . implode(', ', $typeFail);
            return FALSE;
        }
        
        return TRUE;
    }
    
    
    /**
     * Get validation error message
     * 
     * @return string
     */
    public function getValidationError()
    {
        return $this->validationError;
    }
    
    
    /**
     * Persist message to storage
     * 
     * @param string $Message
     * @return boolean
     */
    public function persist($Message)
    {
        $this->persistError = '';
        
        $message = json_decode($Message, TRUE);

        $index = $this->config['index_settings']['index'];
        $mappings = $this->config['index_settings']['body']['mappings'];
        $type = array_keys($mappings)[0];
        
        $params = [
            'index' => $index,
            'type'  => $type,
            'body'  => $message
        ];
        
        $response = $this->client->index($params);
        
        if ($response['result'] === 'created') {
            return TRUE;
            
        } else {
            $this->persistError = 'Document not indexed, result: ' . $response['result'];
            return FALSE;
        }
    }
    
    
    /**
     * Get persist error message
     * 
     * @return string
     */
    public function getPersistError()
    {
        return $this->persistError;
    }
}