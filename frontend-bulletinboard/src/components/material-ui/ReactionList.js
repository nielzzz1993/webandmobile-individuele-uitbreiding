import { connect } from "react-redux";
import Reactions from "./Reactions";

const mapStateToProps = state => ({
  reactions: state.reaction.json
});

export default connect(mapStateToProps)(Reactions);
