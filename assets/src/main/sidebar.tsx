import {observer} from 'mobx-react-lite';
import {useLocation} from 'react-router';
import {useStore} from "./context-provider";
import {Link as ReactLink} from "react-router-dom";
import React from "react";
import {Button} from "@mui/material";
import Typography from "@mui/material/Typography";
import {theme} from "./theme";
import {userSidebarRoutes} from "../user/routes/user-sidebar-routes";


type SidebarRoute = {
    title: string;
    url: string;
    search?: object;
    role: string;
};

const routes: SidebarRoute[] = [
    ...userSidebarRoutes,
];

export const Sidebar = observer(() => {
    const currentPath = useLocation().pathname;
    const {querySerializer, authStore} = useStore();

    return (
        <div className="pt-1 bg-gray-300">
            <div
                className="font-bold text-2xl pb-4 text-center"
            >
                <ReactLink to={"/"}>
                    <div className="flex flex-col gap-1">
                        <img loading={"lazy"} src="/images/optimized/logo.webp" className="w-36" alt={"logo"}/>
                        <Typography variant={"h5"} fontFamily={"Calibri"} fontWeight={"light"}
                                    color={theme.colorDarkGreen}>ZooD_Web</Typography>
                        <Button variant={"text"}>
                            <Typography color={theme.colorDarkGreen}>Главная</Typography>
                        </Button>
                    </div>
                </ReactLink>

            </div>
            <div className="flex flex-col gap-1">
                {routes.map((route) => {
                    const isActive = currentPath.startsWith(route.url);
                    if (!authStore.checkPermission(route.role)) {
                        return null;
                    }
                    return (
                        <ReactLink to={
                            {
                                pathname: route.url,
                                search: route.search ? querySerializer.stringifyParams(route.search) : undefined,
                            }
                        } key={route.url}>
                            <Button variant={"contained"} disabled={!isActive}>
                                {route.title}
                            </Button>
                        </ReactLink>
                    );
                })}
            </div>
        </div>
    )
});


