import { config } from "../config";
import { reactionConstants } from "../constants";

export const reactionActions = {
  addReaction,
  fetchReactions
};

export function addReaction(messageId, content) {
  return function(dispatch) {
    dispatch({ type: reactionConstants.ADD_REACTION });
    return fetch(
      `${
        config.SERVER_ADDRESS
      }/reaction-add?messageId=${messageId}&reaction=${content}`,
      {
        method: "POST"
      }
    );
  };
}

export function fetchReactions(channel) {
  return function(dispatch) {
    dispatch({
      type: reactionConstants.REQUEST_REACTIONS
    });
    return fetch(`${config.SERVER_ADDRESS}/reaction-content/${channel}`)
      .then(
        response => response.json(),
        error => console.log("An error occurred.", error)
      )
      .then(json => {
        dispatch({
          type: reactionConstants.RECEIVE_REACTIONS,
          json: json
        });
      });
  };
}
