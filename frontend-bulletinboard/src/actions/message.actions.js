import { config } from "../config";
import { messageConstants } from "../constants";

export const messageActions = {
  addPost,
  fetchPosts,
  searchPosts,
  searchPostsCategory,
  increaseUpvotes,
  increaseDownvotes
};

export function addPost(userId, title, content, category) {
  fetch(
    `${
      config.SERVER_ADDRESS
    }/message-add?userId=${userId}&title=${title}&content=${content}&category=${category}`,
    {
      method: "POST"
    }
  );
}

export function fetchPosts(channel) {
  return function(dispatch) {
    dispatch({
      type: messageConstants.REQUEST_POSTS
    });

    return fetch(`${config.SERVER_ADDRESS}/${channel}`)
      .then(
        response => response.json(),
        error => console.log("An error occurred.", error)
      )
      .then(json => {
        dispatch({
          type: messageConstants.RECEIVE_POSTS,
          json: json
        });
      });
  };
}

export function searchPosts(keywords) {
  return function(dispatch) {
    dispatch({
      type: messageConstants.REQUEST_SEARCH_POSTS
    });

    return fetch(`${config.SERVER_ADDRESS}/messages-keyword/${keywords}`)
      .then(
        response => response.json(),
        error => console.log("An error occurred.", error)
      )
      .then(json => {
        dispatch({
          type: messageConstants.RECEIVE_SEARCH_POSTS,
          json: json
        });
      });
  };
}

export function searchPostsCategory(keywords, category) {
  return function(dispatch) {
    dispatch({
      type: messageConstants.REQUEST_SEARCH_POSTS_CATEGORY
    });

    return fetch(
      `${
        config.SERVER_ADDRESS
      }/messages-category-keyword?category=${category}&keywords=${keywords}`
    )
      .then(
        response => response.json(),
        error => console.log("An error occurred.", error)
      )
      .then(json => {
        dispatch({
          type: messageConstants.RECEIVE_SEARCH_POSTS_CATEGORY,
          json: json
        });
      });
  };
}

export function increaseUpvotes(channel) {
  return function(dispatch) {
    dispatch({
      type: messageConstants.INCREASE_UPVOTES
    });

    return fetch(
      `${config.SERVER_ADDRESS}/messages-increase-upvotes/${channel}`,
      {
        method: "POST"
      }
    );
  };
}

export function increaseDownvotes(channel) {
  return function(dispatch) {
    dispatch({
      type: messageConstants.INCREASE_DOWNVOTES
    });

    return fetch(
      `${config.SERVER_ADDRESS}/messages-increase-downvotes/${channel}`,
      {
        method: "POST"
      }
    );
  };
}
