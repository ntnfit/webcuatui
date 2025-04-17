import React, { useState, useEffect } from "react";
import { Link, router, usePage } from "@inertiajs/react";
import { motion } from "framer-motion";
import {
    Search,
    Star,
    ChevronLeft,
    ChevronRight,
    FileText,
    Megaphone,
    Lightbulb,
    ArrowRight,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Avatar } from "@/components/ui/avatar";
import Navbar from "@/components/Navbar";

interface BlogPost {
    id: number;
    title: string;
    excerpt: string;
    categories: string[];
    tags: string[];
    type: string;
    slug: string;
    author: {
        name: string;
        avatar: string;
    };
    date: string;
    stars: number;
    featured_image?: string;
    thumbnail_url?: string;
}

interface PaginationProps {
    currentPage: number;
    lastPage: number;
    perPage: number;
    total: number;
}

interface FiltersProps {
    search: string | null;
    type: string | null;
    category: string | null;
}

interface PageProps {
    posts: BlogPost[];
    pagination: PaginationProps;
    filters: FiltersProps;
    categories: string[]; // Categories from backend
}

// Map backend types to Vietnamese display values
const typeMapping: Record<string, string> = {
    article: "Bài viết",
    news: "Tin tức",
    trick: "Mẹo",
};

// Map Vietnamese display values back to backend values for filtering
const typeReverseMapping: Record<string, string> = {
    "Bài viết": "article",
    "Tin tức": "news",
    Mẹo: "trick",
};

const typeIcons = {
    "Bài viết": <FileText className="h-4 w-4" />,
    "Tin tức": <Megaphone className="h-4 w-4" />,
    Mẹo: <Lightbulb className="h-4 w-4" />,
};

// Thêm hàm để tạo slug từ tên category
const createSlug = (text: string): string => {
    return text
        .toLowerCase()
        .normalize("NFD") // Chuẩn hóa Unicode, phân tách các dấu thành các ký tự riêng biệt
        .replace(/[\u0300-\u036f]/g, "") // Loại bỏ các dấu
        .replace(/đ/g, "d")
        .replace(/Đ/g, "D") // Xử lý đặc biệt cho chữ đ/Đ
        .replace(/[^a-z0-9\s]/g, "") // Chỉ giữ lại chữ cái, số và khoảng trắng
        .replace(/\s+/g, "-") // Thay thế khoảng trắng bằng dấu gạch ngang
        .trim(); // Xóa khoảng trắng ở đầu và cuối
};

const Blogs: React.FC = () => {
    const { posts, pagination, filters, categories } = usePage()
        .props as unknown as PageProps;
    const [searchQuery, setSearchQuery] = useState(filters.search || "");
    const [selectedType, setSelectedType] = useState(
        filters.type ? typeMapping[filters.type] || "" : "",
    );

    // Thay đổi: Sử dụng mảng để lưu nhiều category
    const [selectedCategories, setSelectedCategories] = useState<string[]>(
        () => {
            // Khởi tạo từ filters hiện tại
            if (filters.category) {
                // Xử lý khi có category từ URL
                // Dựa vào BE trả về, có thể cần chuyển slug trở lại thành tên hiển thị
                const slugs = filters.category.split(",");
                // Tìm các category trùng khớp từ danh sách có sẵn
                const matchedCategories = categories.filter((cat) =>
                    slugs.includes(createSlug(cat)),
                );
                return matchedCategories.length > 0 ? matchedCategories : [];
            }
            return []; // Mặc định không chọn category nào
        },
    );

    // Add "Tất cả" to categories if it's not already included
    const allCategories = categories.includes("Tất cả")
        ? categories
        : ["Tất cả", ...categories];

    // Transform backend type to frontend display type (Vietnamese)
    const getDisplayType = (backendType: string): string => {
        return typeMapping[backendType] || backendType;
    };

    const applyFilters = () => {
        // Create query params object
        const params: Record<string, string> = {};

        // Add search query if any
        if (searchQuery) {
            params.search = searchQuery;
        }

        // Add type filter
        if (selectedType) {
            params.type = typeReverseMapping[selectedType] || selectedType;
        }

        // Add multiple categories if any
        if (selectedCategories.length > 0) {
            params.category = selectedCategories
                .map((cat) => createSlug(cat))
                .join(",");
        }

        console.log("Applying filters with params:", params);

        // Navigate with filters
        router.get("/blogs", params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle search input with debounce
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (searchQuery !== filters.search) {
                applyFilters();
            }
        }, 500);

        return () => clearTimeout(timeoutId);
    }, [searchQuery]);

    // Handle type selection
    const handleTypeSelect = (type: string) => {
        // Nếu đang chọn loại đã được chọn, bỏ chọn (set về '')
        const newType = selectedType === type ? "" : type;
        setSelectedType(newType);

        // Thực hiện filter ngay lập tức thay vì dùng setTimeout
        const params: Record<string, string> = {};

        if (searchQuery) {
            params.search = searchQuery;
        }

        // Thêm type vào params nếu có loại được chọn
        if (newType) {
            params.type = typeReverseMapping[newType] || newType;
        }

        // Thêm nhiều category vào params nếu có chọn, dùng slug
        if (selectedCategories.length > 0) {
            params.category = selectedCategories
                .map((cat) => createSlug(cat))
                .join(",");
        }

        console.log("Type selected:", newType);
        console.log("Params being sent:", params);

        router.get("/blogs", params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle category selection - đã cập nhật để hỗ trợ nhiều category
    const handleCategorySelect = (category: string) => {
        let newSelectedCategories: string[];

        if (category === "Tất cả") {
            // Nếu chọn "Tất cả", xóa tất cả lựa chọn khác
            newSelectedCategories = [];
        } else if (selectedCategories.includes(category)) {
            // Nếu category đã được chọn, bỏ chọn nó
            newSelectedCategories = selectedCategories.filter(
                (c) => c !== category,
            );
        } else {
            // Nếu category chưa được chọn, thêm vào danh sách đã chọn
            newSelectedCategories = [...selectedCategories, category];
        }

        // Cập nhật state
        setSelectedCategories(newSelectedCategories);

        // Thực hiện filter ngay lập tức
        const params: Record<string, string> = {};

        if (searchQuery) {
            params.search = searchQuery;
        }

        if (selectedType) {
            params.type = typeReverseMapping[selectedType] || selectedType;
        }

        // Thêm nhiều category vào params nếu có chọn, dùng slug
        if (newSelectedCategories.length > 0) {
            params.category = newSelectedCategories
                .map((cat) => createSlug(cat))
                .join(",");
        }

        console.log("Categories selected:", newSelectedCategories);
        console.log(
            "Category slugs:",
            newSelectedCategories.map((cat) => createSlug(cat)),
        );
        console.log("Params being sent:", params);

        router.get("/blogs", params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle pagination
    const paginate = (pageNumber: number) => {
        const params: Record<string, string | number> = {
            page: pageNumber,
        };

        if (searchQuery) params.search = searchQuery;
        if (selectedType)
            params.type = typeReverseMapping[selectedType] || selectedType;

        // Add multiple categories if any
        if (selectedCategories.length > 0) {
            params.category = selectedCategories
                .map((cat) => createSlug(cat))
                .join(",");
        }

        console.log("Paginating with params:", params);

        router.get("/blogs", params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const filterVariants = {
        selected: {
            scale: 1.05,
            transition: { duration: 0.3 },
        },
        notSelected: {
            scale: 1,
            transition: { duration: 0.3 },
        },
    };

    const postVariants = {
        hidden: { opacity: 0, y: 20 },
        visible: (i: number) => ({
            opacity: 1,
            y: 0,
            transition: {
                delay: i * 0.1,
                duration: 0.5,
            },
        }),
    };

    const getTypeColor = (type: string) => {
        switch (type) {
            case "Bài viết":
                return {
                    light: "bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100",
                    dark: "dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40",
                };
            case "Tin tức":
                return {
                    light: "bg-purple-50 text-purple-600 border-purple-200 hover:bg-purple-100",
                    dark: "dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800/50 dark:hover:bg-purple-900/40",
                };
            case "Mẹo":
                return {
                    light: "bg-amber-50 text-amber-600 border-amber-200 hover:bg-amber-100",
                    dark: "dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50 dark:hover:bg-amber-900/40",
                };
            default:
                return {
                    light: "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100",
                    dark: "dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600",
                };
        }
    };

    const TypeButton = ({ type }: { type: string }) => {
        const colors = getTypeColor(type);
        const isSelected = selectedType === type;

        return (
            <motion.div
                initial="notSelected"
                animate={isSelected ? "selected" : "notSelected"}
                variants={filterVariants}
                className="px-1 py-1"
            >
                <Button
                    variant={isSelected ? "default" : "outline"}
                    className={`rounded-full border px-4 py-2 ${colors.light} ${colors.dark} transition-colors duration-300`}
                    onClick={() => handleTypeSelect(type)}
                >
                    {type === "Bài viết" && typeIcons["Bài viết"]}
                    {type === "Tin tức" && typeIcons["Tin tức"]}
                    {type === "Mẹo" && typeIcons["Mẹo"]}
                    {type}
                </Button>
            </motion.div>
        );
    };

    const getCategoryColor = (category: string) => {
        // Thay đổi: Kiểm tra category có nằm trong danh sách đã chọn hay không
        const isSelected = selectedCategories.includes(category);

        if (isSelected) {
            return {
                light: "bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100",
                dark: "dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40",
            };
        }
        return {
            light: "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100",
            dark: "dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600",
        };
    };

    type TagColors = {
        [key: string]: {
            light: string;
            dark: string;
        };
    };

    const getTagColor = (tag: string) => {
        const colors: TagColors = {
            React: {
                light: "bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100",
                dark: "dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40",
            },
            "Node.js": {
                light: "bg-green-50 text-green-600 border-green-200 hover:bg-green-100",
                dark: "dark:bg-green-900/30 dark:text-green-400 dark:border-green-800/50 dark:hover:bg-green-900/40",
            },
            TailwindCSS: {
                light: "bg-cyan-50 text-cyan-600 border-cyan-200 hover:bg-cyan-100",
                dark: "dark:bg-cyan-900/30 dark:text-cyan-400 dark:border-cyan-800/50 dark:hover:bg-cyan-900/40",
            },
            MongoDB: {
                light: "bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100",
                dark: "dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50 dark:hover:bg-emerald-900/40",
            },
            "Next.js": {
                light: "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100",
                dark: "dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600",
            },
            Express: {
                light: "bg-amber-50 text-amber-600 border-amber-200 hover:bg-amber-100",
                dark: "dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50 dark:hover:bg-amber-900/40",
            },
            TypeScript: {
                light: "bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100",
                dark: "dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40",
            },
            Laravel: {
                light: "bg-red-50 text-red-600 border-red-200 hover:bg-red-100",
                dark: "dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/50 dark:hover:bg-red-900/40",
            },
            Frontend: {
                light: "bg-purple-50 text-purple-600 border-purple-200 hover:bg-purple-100",
                dark: "dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800/50 dark:hover:bg-purple-900/40",
            },
            Backend: {
                light: "bg-orange-50 text-orange-600 border-orange-200 hover:bg-orange-100",
                dark: "dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800/50 dark:hover:bg-orange-900/40",
            },
            FullStack: {
                light: "bg-indigo-50 text-indigo-600 border-indigo-200 hover:bg-indigo-100",
                dark: "dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800/50 dark:hover:bg-indigo-900/40",
            },
        };
        return colors[tag] || colors["Next.js"];
    };

    return (
        <div className="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
            <Navbar />
            <div className="pt-16">
                <div className="max-w-7xl mx-auto px-4 py-8">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5 }}
                        className="mb-8"
                    >
                        <h1 className="text-4xl font-bold text-apple-dark-gray dark:text-white mb-8 transition-colors duration-500">
                            Blog
                        </h1>

                        {/* Type Filters */}
                        <div className="flex flex-wrap md:flex-nowrap gap-2 mb-6 overflow-x-auto pb-2">
                            <motion.div
                                initial="notSelected"
                                animate={
                                    selectedType === ""
                                        ? "selected"
                                        : "notSelected"
                                }
                                variants={filterVariants}
                                className="px-1 py-1"
                            >
                                <Button
                                    variant={
                                        selectedType === ""
                                            ? "default"
                                            : "outline"
                                    }
                                    className={`rounded-full border px-4 py-2 ${
                                        selectedType === ""
                                            ? "bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40"
                                            : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                    } transition-colors duration-300`}
                                    onClick={() => handleTypeSelect("")}
                                >
                                    Tất cả
                                </Button>
                            </motion.div>
                            <TypeButton type="Bài viết" />
                            <TypeButton type="Tin tức" />
                            <TypeButton type="Mẹo" />
                        </div>

                        {/* Search */}
                        <div className="relative flex-1 mb-8">
                            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 transition-colors duration-500" />
                            <input
                                type="text"
                                placeholder="Tìm kiếm bài viết..."
                                className="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-apple-dark-gray dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-apple-blue dark:focus:ring-blue-500 transition-colors duration-500"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                            />
                        </div>

                        {/* Categories */}
                        <div className="mb-8">
                            <h2 className="text-xl font-semibold mb-4 text-apple-dark-gray dark:text-white transition-colors duration-500">
                                Danh mục
                            </h2>
                            <div className="flex flex-wrap gap-2">
                                {/* Nút "Tất cả" */}
                                <motion.div
                                    key="all"
                                    initial="notSelected"
                                    animate={
                                        selectedCategories.length === 0
                                            ? "selected"
                                            : "notSelected"
                                    }
                                    variants={filterVariants}
                                    whileHover={{ scale: 1.05 }}
                                    className="px-1 py-1"
                                >
                                    <Button
                                        variant={
                                            selectedCategories.length === 0
                                                ? "default"
                                                : "outline"
                                        }
                                        className={`rounded-full border px-4 py-2 ${
                                            selectedCategories.length === 0
                                                ? "bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40"
                                                : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                        } transition-colors duration-300`}
                                        onClick={() =>
                                            handleCategorySelect("Tất cả")
                                        }
                                    >
                                        Tất cả
                                    </Button>
                                </motion.div>

                                {/* Các nút category khác */}
                                {allCategories
                                    .filter((category) => category !== "Tất cả")
                                    .map((category) => {
                                        const colors =
                                            getCategoryColor(category);
                                        return (
                                            <motion.div
                                                key={category}
                                                initial="notSelected"
                                                animate={
                                                    selectedCategories.includes(
                                                        category,
                                                    )
                                                        ? "selected"
                                                        : "notSelected"
                                                }
                                                variants={filterVariants}
                                                whileHover={{ scale: 1.05 }}
                                                className="px-1 py-1"
                                            >
                                                <Button
                                                    variant={
                                                        selectedCategories.includes(
                                                            category,
                                                        )
                                                            ? "default"
                                                            : "outline"
                                                    }
                                                    className={`rounded-full border px-4 py-2 ${colors.light} ${colors.dark} transition-colors duration-300`}
                                                    onClick={() =>
                                                        handleCategorySelect(
                                                            category,
                                                        )
                                                    }
                                                >
                                                    {category}
                                                </Button>
                                            </motion.div>
                                        );
                                    })}
                            </div>
                        </div>

                        {/* Results Count */}
                        <div className="text-gray-600 dark:text-gray-400 mb-6 transition-colors duration-500">
                            {posts.length > 0 ? (
                                <>
                                    Hiển thị{" "}
                                    {(pagination.currentPage - 1) *
                                        pagination.perPage +
                                        1}{" "}
                                    đến{" "}
                                    {Math.min(
                                        pagination.currentPage *
                                            pagination.perPage,
                                        pagination.total,
                                    )}{" "}
                                    trong tổng số {pagination.total} kết quả
                                </>
                            ) : (
                                <>Không tìm thấy kết quả nào phù hợp.</>
                            )}
                        </div>

                        {/* Blog Posts */}
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                            {posts.map((post, index) => {
                                // Convert backend type to display type
                                const displayType = getDisplayType(post.type);
                                const typeColors = getTypeColor(displayType);

                                return (
                                    <div
                                        key={post.id}
                                        className="h-full relative group"
                                    >
                                        {/* Dash border element that appears on hover */}
                                        <div className="absolute inset-0 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                        <motion.div
                                            variants={postVariants}
                                            initial="hidden"
                                            animate="visible"
                                            custom={index}
                                            className="h-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-300 group-hover:shadow-md dark:group-hover:shadow-gray-800/50 group-hover:translate-x-[-10px] group-hover:translate-y-[-10px]"
                                        >
                                            {/* Hình ảnh với thứ tự ưu tiên: thumbnail > featured_image > placeholder */}
                                            <Link
                                                href={`/blogs/${post.slug}`}
                                                className="block overflow-hidden h-48 relative"
                                            >
                                                <img
                                                    src={
                                                        post.thumbnail_url ||
                                                        post.featured_image ||
                                                        "https://via.placeholder.com/800x400?text=Blog+Image"
                                                    }
                                                    alt={post.title}
                                                    className="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                                                    loading="lazy"
                                                    onError={(e) => {
                                                        (
                                                            e.target as HTMLImageElement
                                                        ).src =
                                                            "https://via.placeholder.com/800x400?text=Blog+Image";
                                                    }}
                                                />
                                            </Link>

                                            <div className="p-6 flex flex-col h-[calc(100%-12rem)]">
                                                <div className="flex items-start justify-between flex-1">
                                                    <div className="flex-1">
                                                        {/* Type badge */}
                                                        <div className="flex items-center gap-2 mb-2">
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                className={`rounded-full border ${typeColors.light} ${typeColors.dark} transition-colors duration-300`}
                                                            >
                                                                {displayType ===
                                                                    "Bài viết" &&
                                                                    typeIcons[
                                                                        "Bài viết"
                                                                    ]}
                                                                {displayType ===
                                                                    "Tin tức" &&
                                                                    typeIcons[
                                                                        "Tin tức"
                                                                    ]}
                                                                {displayType ===
                                                                    "Mẹo" &&
                                                                    typeIcons[
                                                                        "Mẹo"
                                                                    ]}
                                                                {displayType}
                                                            </Button>
                                                        </div>

                                                        {/* Title (clickable) */}
                                                        <h3 className="mb-2">
                                                            <Link
                                                                href={`/blogs/${post.slug}`}
                                                                className="text-xl font-semibold text-apple-dark-gray dark:text-white line-clamp-1 group-hover:text-apple-blue dark:group-hover:text-blue-400 transition-colors"
                                                            >
                                                                {post.title}
                                                            </Link>
                                                        </h3>

                                                        {/* Excerpt */}
                                                        <p className="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3 transition-colors duration-500">
                                                            {post.excerpt}
                                                        </p>

                                                        {/* Category tags */}
                                                        <div className="flex flex-wrap gap-2 mb-4">
                                                            {post.categories.map(
                                                                (cat, i) => {
                                                                    const colors =
                                                                        getTagColor(
                                                                            cat,
                                                                        );
                                                                    return (
                                                                        <motion.span
                                                                            key={`${post.id}-category-${i}`}
                                                                            className={`inline-block px-3 py-1 rounded-full text-xs border ${colors.light} ${colors.dark} transition-colors duration-300`}
                                                                            whileHover={{
                                                                                scale: 1.05,
                                                                            }}
                                                                            transition={{
                                                                                duration: 0.3,
                                                                            }}
                                                                        >
                                                                            {
                                                                                cat
                                                                            }
                                                                        </motion.span>
                                                                    );
                                                                },
                                                            )}
                                                        </div>

                                                        {/* Hiển thị tags nếu có */}
                                                        {post.tags &&
                                                            post.tags.length >
                                                                0 && (
                                                                <div className="flex flex-wrap gap-1.5 mb-4">
                                                                    {post.tags.map(
                                                                        (
                                                                            tag,
                                                                            i,
                                                                        ) => (
                                                                            <span
                                                                                key={`${post.id}-tag-${i}`}
                                                                                className="inline-block px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded transition-colors duration-300"
                                                                            >
                                                                                #
                                                                                {
                                                                                    tag
                                                                                }
                                                                            </span>
                                                                        ),
                                                                    )}
                                                                </div>
                                                            )}
                                                    </div>

                                                    {/* Star count */}
                                                    <div className="flex items-center gap-1 text-amber-500 dark:text-amber-400 transition-colors duration-500">
                                                        <Star className="h-4 w-4 fill-current" />
                                                        <span>
                                                            {post.stars}
                                                        </span>
                                                    </div>
                                                </div>

                                                {/* Footer: avatar, author, date, button */}
                                                <div className="flex items-center justify-between mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                                                    <div className="flex items-center gap-2">
                                                        <Avatar>
                                                            <img
                                                                src={
                                                                    post.author
                                                                        .avatar
                                                                }
                                                                alt={
                                                                    post.author
                                                                        .name
                                                                }
                                                                onError={(
                                                                    e,
                                                                ) => {
                                                                    (
                                                                        e.target as HTMLImageElement
                                                                    ).src =
                                                                        "https://github.com/shadcn.png";
                                                                }}
                                                            />
                                                        </Avatar>
                                                        <div>
                                                            <div className="text-sm font-medium text-apple-dark-gray dark:text-white transition-colors duration-500">
                                                                {
                                                                    post.author
                                                                        .name
                                                                }
                                                            </div>
                                                            <div className="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-500">
                                                                {post.date}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {/* Button "Đọc thêm" */}
                                                    <Link
                                                        href={`/blogs/${post.slug}`}
                                                        className="
                                                            inline-flex items-center gap-2
                                                            px-5 py-2
                                                            border-2 border-apple-blue dark:border-blue-400
                                                            text-apple-blue dark:text-blue-400
                                                            bg-transparent
                                                            hover:bg-apple-blue/10 dark:hover:bg-blue-400/20
                                                            rounded-full
                                                            transition-all duration-300
                                                            focus:outline-none focus:ring-2 focus:ring-apple-blue/50 dark:focus:ring-blue-400/50
                                                            "
                                                    >
                                                        <span className="font-medium">
                                                            Đọc thêm
                                                        </span>
                                                        <ArrowRight
                                                            className="
                                                                h-5 w-5
                                                                text-apple-blue dark:text-blue-400
                                                                transition-colors duration-200
                                                                hover:text-apple-blue/80 dark:hover:text-blue-500
                                                            "
                                                        />
                                                    </Link>
                                                </div>
                                            </div>
                                        </motion.div>
                                    </div>
                                );
                            })}
                        </div>

                        {/* Pagination */}
                        <div className="flex items-center justify-center gap-2 mt-8">
                            <Button
                                variant="outline"
                                size="icon"
                                onClick={() =>
                                    pagination.currentPage > 1 &&
                                    paginate(pagination.currentPage - 1)
                                }
                                disabled={pagination.currentPage === 1}
                                className="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-apple-dark-gray dark:text-gray-200 border-gray-200 dark:border-gray-700 transition-colors duration-300"
                            >
                                <ChevronLeft className="h-4 w-4" />
                            </Button>

                            {Array.from({ length: pagination.lastPage }).map(
                                (_, index) => (
                                    <Button
                                        key={`page-${index + 1}`}
                                        variant={
                                            pagination.currentPage === index + 1
                                                ? "default"
                                                : "outline"
                                        }
                                        className={`w-10 h-10 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-apple-dark-gray dark:text-gray-200 border-gray-200 dark:border-gray-700 transition-colors duration-300 ${pagination.currentPage === index + 1 ? "bg-rose-500 dark:bg-rose-600 hover:bg-rose-600 dark:hover:bg-rose-700 text-white" : ""}`}
                                        onClick={() => paginate(index + 1)}
                                    >
                                        {index + 1}
                                    </Button>
                                ),
                            )}

                            <Button
                                variant="outline"
                                size="icon"
                                onClick={() =>
                                    pagination.currentPage <
                                        pagination.lastPage &&
                                    paginate(pagination.currentPage + 1)
                                }
                                disabled={
                                    pagination.currentPage ===
                                    pagination.lastPage
                                }
                                className="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-apple-dark-gray dark:text-gray-200 border-gray-200 dark:border-gray-700 transition-colors duration-300"
                            >
                                <ChevronRight className="h-4 w-4" />
                            </Button>
                        </div>
                    </motion.div>
                </div>
            </div>
        </div>
    );
};

export default Blogs;
