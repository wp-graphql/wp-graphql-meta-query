#WPGraphQL Meta Query

This plugin adds Meta_Query support to the WP GraphQL Plugin for postObject query args. 

## Pre-req's
Using this plugin requires having the <a href="https://github.com/wp-graphql/wp-graphql" target="_blank">WPGraphQL plugin</a> installed 
and activated. 

## Activating / Using
Activate the plugin like you would any other WordPress plugin. 

Once the plugin is active, the `metaQuery` argument will be available to any post object connectionQuery 
(posts, pages, custom post types, etc).

## Example Query
Below is an example Query using the metaQuery input on a `posts` query. (Go ahead and check things out in <a target="_blank" href="https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij?hl=en">GraphiQL</a>)

This will find `posts` that have `some_value` as the value of the post_meta field with the key of `some_key` AND also 
DOES NOT have `some_other_value` as the value for the post_meta key `some_other_key`

```
query{
  posts(
    where: {
      metaQuery: {
        relation: AND
        metaArray: [
          {
            key: "some_key",
            value: "some_value"
            compare: EQUAL_TO
          },
          {
            key: "some_other_key",
            value: "some_other_value",
            compare: NOT_EQUAL_TO
          }
        ]
      }
  	}
  ){
    edges{
      cursor
      node{
        id
        postId
        link
        date
      }
    }
  }
}
```

The same query in PHP using WP_Query would look like: 

```
$args = [
    'meta_query' => [
        'relation' => 'AND',
        [
            'key' => 'some_key',
            'value' => 'some_value',
            'compare' => 'EQUAL TO',
        ],
        [
            'key' => 'some_other_key',
            'value' => 'some_other_value',
            'compare' => 'NOT EQUAL TO',
        ],
    ],
];

new WP_Query( $args );
```