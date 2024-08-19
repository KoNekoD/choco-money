import React, {useState} from "react";
import {Button, TextField} from "@mui/material";
import {useStore} from "../../../main/context-provider";
import {observer} from "mobx-react-lite";
import {Form} from "react-router-dom";

export const LoginPage = observer(() => {
    const {authStore} = useStore();

    const [login, setLogin] = useState("");
    const [password, setPassword] = useState("");

    const [formLoading, setFormLoading] = useState(false);

    function handleSubmit() {
        setFormLoading(true);
        authStore.submitLoginForm({login, password}).then(() => setFormLoading(false));
    }

    return (
        <Form onSubmit={handleSubmit}>
            <div className="flex justify-center items-center">
                <div className="flex items-center justify-center flex-col gap-2">
                    <div className="font-light text-2xl text-center">Вход</div>
                    <TextField
                        label="Логин"
                        variant="outlined"
                        value={login}
                        onChange={(event) => setLogin(event.target.value)}
                    />
                    <TextField
                        label="Пароль"
                        variant="outlined"
                        value={password}
                        onChange={(event) => setPassword(event.target.value)}
                    />
                    <div className="flex justify-center w-2/3">
                        <Button variant="outlined" type={"submit"} disabled={formLoading}>
                            Войти
                        </Button>
                    </div>
                </div>
            </div>
        </Form>
    );
});
