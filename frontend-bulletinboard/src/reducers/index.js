import { combineReducers } from "redux";
//Reducers
import messageReducer from "./message.reducer";
import reactionReducer from "./reaction.reducer";

export default combineReducers({
  message: messageReducer,
  reaction: reactionReducer
});
