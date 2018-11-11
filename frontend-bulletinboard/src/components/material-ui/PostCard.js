//React
import React, { Fragment } from "react";
//MaterialUI components
import Card from "@material-ui/core/Card";
import CardActions from "@material-ui/core/CardActions";
import CardContent from "@material-ui/core/CardContent";
import IconButton from "@material-ui/core/IconButton";
import LinkButton from "./LinkButton";
//MaterialUI Icons
import ArrowDownwardIcon from "@material-ui/icons/ArrowDownward";
import ArrowUpwardIcon from "@material-ui/icons/ArrowUpward";
import Typography from "@material-ui/core/Typography";
import { getLoggedIn } from "../LoginCookie";

export default function PostCard(props) {
  let cardActions;
  if (getLoggedIn() === "true") {
    cardActions = (
      <CardActions>
        <IconButton
          color="primary"
          aria-label="Upvote"
          onClick={() => {
            props.increaseUpvotes(props.message.id);
          }}
        >
          <ArrowUpwardIcon />
        </IconButton>
        <IconButton
          color="primary"
          aria-label="Downvote"
          onClick={() => {
            props.increaseDownvotes(props.message.id);
          }}
        >
          <ArrowDownwardIcon />
        </IconButton>
      </CardActions>
    );
  } else {
    cardActions = <CardActions />;
  }

  return (
    <Fragment>
      {props.message ? (
        <Card>
          <LinkButton
            style={{ width: "100%", padding: 0 }}
            to={"/reactions/" + props.message.id}
            onClick={() => {
              props.getReactions(props.message.id);
            }}
          >
            <CardContent style={{ width: "100%" }}>
              <Typography variant="h1">{props.message.title}</Typography>
              <Typography variant="body1">{props.message.content}</Typography>
            </CardContent>
          </LinkButton>
          {cardActions}
        </Card>
      ) : null}
    </Fragment>
  );
}
