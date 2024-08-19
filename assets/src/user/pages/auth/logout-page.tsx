import React, {useState} from "react";
import {Button} from "@mui/material";
import {useStore} from "../../../main/context-provider";
import {observer} from "mobx-react-lite";
import {Form} from "react-router-dom";

export const LogoutPage = observer(() => {
    const {authStore} = useStore();

    const [formLoading, setFormLoading] = useState(false);

    function handleSubmit() {
        setFormLoading(true);
        authStore.logout()
    }

    return (
        <Form onSubmit={handleSubmit}>
            <div className="flex justify-center items-center">
                <div className="flex items-center justify-center flex-col gap-2">
                    <div className="font-light text-2xl text-center">Вы уверены?</div>
                    <div className="flex justify-center w-2/3">
                        <Button variant="outlined" type={"submit"} disabled={formLoading}>
                            Да, выйти
                        </Button>
                    </div>
                </div>
            </div>
        </Form>
    );
});