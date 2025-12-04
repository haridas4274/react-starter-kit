import { useState } from "react";
import { Head, useForm } from "@inertiajs/react";
import { useEcho } from "@laravel/echo-react";
import { store } from "@/actions/App/Http/Controllers/TaskController";

export default function Index({ liveTasks }) {
    const [tasks, setTasks] = useState(liveTasks ?? []);
    useEcho(
        "tasks",
        ["TaskCreated"],
        ({ task }: { task: unknown }) => {
            setTasks((prev) => [task, ...prev]);
        }
    ) .channel()
        .subscribed(() => console.log("🟢 Connected to private:tasks"))
        .error((err) => console.log("❌ Subscription error:", err));
    const { data, setData, processing, reset, submit } = useForm({
        title: "",
    });

    const onSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit(store(), {
            onSuccess: () => reset("title"), // clear input after storing
        });
    };
    return (
        <>
            <Head title="Tasks" />

            <div className="max-w-2xl mx-auto mt-8 space-y-6">
                {/* SPA Form (no page reload) */}
                <form onSubmit={onSubmit} className="flex gap-2">
                    <input
                        className="border px-3 py-2 flex-1 rounded"
                        placeholder="New task title"
                        value={data.title}
                        onChange={(e) => setData("title", e.target.value)}
                    />
                    <button
                        type="submit"
                        disabled={processing}
                        className="bg-blue-600 text-white px-4 py-2 rounded"
                    >
                        Add
                    </button>
                </form>

                {/* Live task list */}
                <ul className="space-y-2">
                    {tasks.map((task) => (
                        <li
                            key={task.id}
                            className="border rounded px-3 py-2 flex justify-between"
                        >
                            <span>{task.title}</span>
                            <span className="text-xs text-gray-500">#{task.id}</span>
                        </li>
                    ))}
                </ul>
            </div>
        </>
    );
}
