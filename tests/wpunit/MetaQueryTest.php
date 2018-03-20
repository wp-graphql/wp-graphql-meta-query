<?php

class MetaQueryTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp()
    {
        // before
        parent::setUp();

        // your set up methods here
    }

    public function tearDown()
    {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    // tests
    public function testMetaQuery()
    {

	    $unique = uniqid();

    	$this->factory()->post->create([
    		'post_status' => 'publish',
		    'post_type' => 'post',
		    'post_title' => 'Test Meta Query',
		    'meta_input' => [
		    	'test_meta_query' => $unique
		    ]
	    ]);

    	$query = '
    	query GetPostsByMetaQuery($relation: RelationEnum, $key: String, $value: String) {
		  posts(where: {metaQuery: {relation: $relation, metaArray: [{key: $key, value: $value}]}}) {
		    edges {
		      node {
		        id
		        title
		        postId
		      }
		    }
		  }
		}
    	';

    	$variables = [
    		'relation' => 'AND',
		    'key' => 'test_meta_query',
		    'value' => $unique
	    ];

    	$results = do_graphql_request( $query, 'GetPostsByMetaQuery', $variables );

    	$this->assertArrayNotHasKey( 'errors', $results );
    	$this->assertTrue( 1 === count( $results['data']['posts']['edges'] ) );
	    $this->assertEquals( 'Test Meta Query', $results['data']['posts']['edges'][0]['node']['title'] );
    }

}