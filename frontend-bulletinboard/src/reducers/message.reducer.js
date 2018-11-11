import { messageConstants } from "../constants";

export default function messageReducer(state = {}, action) {
  switch (action.type) {
    case messageConstants.REQUEST_POSTS:
      return { ...state, loading: true };
    case messageConstants.INCREASE_UPVOTES:
      return { ...state };
    case messageConstants.INCREASE_DOWNVOTES:
      return { ...state };
    case messageConstants.RECEIVE_POSTS:
      return { ...state, json: action.json, loading: false };
    case messageConstants.REQUEST_SEARCH_POSTS:
      return { ...state, loading: true };
    case messageConstants.RECEIVE_SEARCH_POSTS:
      return { ...state, json: action.json, loading: false };
    case messageConstants.REQUEST_SEARCH_POSTS_CATEGORY:
      return { ...state, loading: true };
    case messageConstants.RECEIVE_SEARCH_POSTS_CATEGORY:
      return { ...state, json: action.json, loading: false };
    default:
      return state;
  }
}
