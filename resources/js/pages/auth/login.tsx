import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

type LoginForm = {
    USER_ID: string;
    PASSWORD: string;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        USER_ID: '',
        PASSWORD: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('PASSWORD'),
        });
    };

    return (
        <AuthLayout title="Log in to your MyUdD Portal" description="Enter your username and password below to log in">
            <Head title="Log in" />

            <form className="flex flex-col gap-6" onSubmit={submit}>
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="USER_ID">Username</Label>
                        <Input
                            id="USER_ID"
                            type="text"
                            required
                            autoFocus
                            value={data.USER_ID}
                            onChange={(e) => setData('USER_ID', e.target.value)}
                            placeholder="cddjuandelacruz"
                        />
                        <InputError message={errors.USER_ID} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="PASSWORD">Password</Label>
                        <Input
                            id="PASSWORD"
                            type="password"
                            required
                            value={data.PASSWORD}
                            onChange={(e) => setData('PASSWORD', e.target.value)}
                            placeholder="••••••••"
                        />
                        <InputError message={errors.PASSWORD} />
                    </div>

                    <Button type="submit" className="mt-4 w-full" disabled={processing}>
                        {processing && <LoaderCircle className="h-4 w-4 animate-spin mr-2" />}
                        Log in
                    </Button>
                </div>
            </form>

            {status && <div className="mt-4 text-center text-sm font-medium text-green-600">{status}</div>}
        </AuthLayout>
    );
}
