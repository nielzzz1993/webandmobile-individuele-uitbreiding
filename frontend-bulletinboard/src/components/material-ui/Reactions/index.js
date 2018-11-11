//React
import React, { Fragment } from "react";
//MaterialUI components
import ReactionCard from "../ReactionCard";
import Typography from "@material-ui/core/Typography";

export default payload => {
  return (
    <Fragment>
      {payload["reactions"].length ? (
        <Fragment>
          {payload["reactions"].map(reaction => (
            <ReactionCard reaction={reaction} />
          ))}
        </Fragment>
      ) : (
        <Typography variant="h1">No reactions found.</Typography>
      )}
    </Fragment>
  );
};
