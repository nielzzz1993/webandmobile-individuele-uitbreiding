//React
import React, { Fragment } from "react";
//MaterialUI components
import Card from "@material-ui/core/Card";
import CardContent from "@material-ui/core/CardContent";
//MaterialUI Icons
import Typography from "@material-ui/core/Typography";

export default function ReactionCard(props) {
  return (
    <Fragment>
      {props.reaction ? (
        <Card>
          <CardContent style={{ width: "100%" }}>
            <Typography variant="body1">{props.reaction.reaction}</Typography>
          </CardContent>
        </Card>
      ) : null}
    </Fragment>
  );
}
