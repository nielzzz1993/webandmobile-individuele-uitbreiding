import { reactionConstants } from "../constants";

export default function reactionReducer(state = {}, action) {
  switch (action.type) {
    case reactionConstants.REQUEST_REACTIONS:
      return { ...state, loading: true };
    case reactionConstants.RECEIVE_REACTIONS:
      return { ...state, json: action.json, loading: false };
    case reactionConstants.ADD_REACTION:
      return { ...state };
    default:
      return state;
  }
}
