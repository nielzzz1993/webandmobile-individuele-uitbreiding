import { connect } from "react-redux";
import Messages from "./Messages";
import { messageActions } from "../../actions/message.actions";
import { reactionActions } from "../../actions/reaction.actions";

const mapStateToProps = state => ({
  messages: state.message.json
});

const mapDispatchToProps = {
  getReactions: reactionActions.fetchReactions,
  getPosts: messageActions.fetchPosts,
  increaseUpvote: messageActions.increaseUpvotes,
  increaseDownvote: messageActions.increaseDownvotes
};

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(Messages);
