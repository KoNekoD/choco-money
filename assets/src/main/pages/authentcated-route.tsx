import {Navigate} from 'react-router-dom'
import {useStore} from "../context-provider";
import React from "react";
import {observer} from "mobx-react-lite";

export const AuthenticatedRoute = observer(({children}: any) => {
    const {authStore} = useStore()
    const isAuthenticated = authStore.isAuthenticated()

    if (!isAuthenticated) {
        return <Navigate to={{pathname: "/login"}}/>
    }

    return children;
});