# WPGraphQL Meta Query
This plugin adds Meta_Query support to the WP GraphQL Plugin for postObject query args.


## Why is this an extension and not part of WPGraphQL?

Meta Queries _can_ be expensive and have been known to actually take sites down, which is why they are not
part of the core WPGraphQL plugin. 

If you need meta queries for your WPGraphQL system, this plugin enables them, but use with caution. It might be better
to hook into WPGraphQL and define specific meta queries that you _know_ you need and are not going to take your system 
down instead of allowing just any meta_query via this plugin, but you could use this plugin as an example of how
to hook into WPGraphQL to add inputs and map those inputs to the WP_Query that gets executed.

## Pre-req's
Using this plugin requires having the <a href="https://github.com/wp-graphql/wp-graphql" target="_blank">WPGraphQL plugin</a> installed 
and activated. Requires WPGraphQL version 0.0.15 or newer.

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