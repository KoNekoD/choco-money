import React, {useState} from "react";
import {Button, TextField} from "@mui/material";
import {observer} from "mobx-react-lite";
import {Form, useNavigate} from "react-router-dom";
import {useStore} from "../../main/context-provider";

export const CreateProfilePage = observer(() => {
    const {userStore} = useStore();
    const navigate = useNavigate();

    const [firstName, setFirstName] = useState("");
    const [lastName, setLastName] = useState("");

    const [formLoading, setFormLoading] = useState(false);

    function handleSubmit() {
        setFormLoading(true);
        userStore.createProfile({firstName, lastName}).then(
            () => {
                setFormLoading(false)
                return navigate('/me');
            }
        );
    }

    return (
        <Form onSubmit={handleSubmit}>
            <div className="flex justify-center items-center">
                <div className="flex items-center justify-center flex-col gap-2">
                    <div className="font-light text-2xl text-center">Создание профиля</div>
                    <TextField
                        label="Имя"
                        variant="outlined"
                        value={firstName}
                        onChange={(event) => setFirstName(event.target.value)}
                    />
                    <TextField
                        label="Фамилия"
                        variant="outlined"
                        value={lastName}
                        onChange={(event) => setLastName(event.target.value)}
                    />
                    <div className="flex justify-center w-2/3">
                        <Button variant="outlined" type={"submit"} disabled={formLoading}>
                            Создать
                        </Button>
                    </div>
                </div>
            </div>
        </Form>
    );
});
