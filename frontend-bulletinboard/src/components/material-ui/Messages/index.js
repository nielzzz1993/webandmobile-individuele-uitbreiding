//React
import React, { Fragment } from "react";
//MaterialUI components
import PostCard from "../PostCard";
import Typography from "@material-ui/core/Typography";

export default payload => {
  return (
    <Fragment>
      {payload["messages"].length ? (
        <Fragment>
          {payload["messages"].map(message => (
            <PostCard
              message={message}
              getReactions={payload["getReactions"]}
              getPosts={payload["getPosts"]}
              increaseUpvotes={payload["increaseUpvote"]}
              increaseDownvotes={payload["increaseDownvote"]}
            />
          ))}
        </Fragment>
      ) : (
        <Typography variant="h1">No posts found.</Typography>
      )}
    </Fragment>
  );
};
